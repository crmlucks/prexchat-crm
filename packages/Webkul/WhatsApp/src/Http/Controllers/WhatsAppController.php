<?php

namespace Webkul\WhatsApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\WhatsApp\Models\Conversation;
use Webkul\WhatsApp\Models\Message;
use Webkul\WhatsApp\Services\WhatsAppService;
use Webkul\WhatsApp\Services\AiService;
use Webkul\WhatsApp\Services\LeadQualificationService;
use Webkul\WhatsApp\Services\LeadSyncService;

class WhatsAppController extends Controller
{
    protected $whatsappService;
    protected $aiService;
    protected $qualificationService;
    protected $leadSyncService;

    public function __construct(
        WhatsAppService $whatsappService,
        AiService $aiService,
        LeadQualificationService $qualificationService,
        LeadSyncService $leadSyncService
    ) {
        $this->whatsappService = $whatsappService;
        $this->aiService = $aiService;
        $this->qualificationService = $qualificationService;
        $this->leadSyncService = $leadSyncService;
    }


    public function index()
    {
        return view('whatsapp::admin.index');
    }

    /**
     * API: List all conversations with last message and qualification data
     */
    public function getConversations()
    {
        $conversations = Conversation::with(['lead', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])->latest()->get();

        return response()->json($conversations);
    }

    /**
     * API: Get all messages for a conversation
     */
    public function getMessages($id)
    {
        $messages = Message::where('conversation_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * API: Get qualification data for a conversation
     */
    public function getQualification($id)
    {
        $conversation = Conversation::findOrFail($id);

        return response()->json([
            'ai_score'       => $conversation->ai_score,
            'intent_level'   => $conversation->intent_level,
            'budget_detected' => $conversation->budget_detected,
            'location_interest' => $conversation->location_interest,
            'property_type'  => $conversation->property_type,
            'qualification_summary' => $conversation->qualification_summary,
            'qualified_at'   => $conversation->qualified_at,
        ]);
    }

    /**
     * API: Manual send from admin panel
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'content'         => 'required',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        // 1. Save locally
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'content'         => $request->content,
            'sender'          => 'bot',
            'message_type'    => 'text',
        ]);

        // 2. Send via external API (-> not .)
        if (is_numeric($conversation->external_id)) {
            $this->whatsappService->sendMetaMessage($conversation->remote_jid, $request->content);
        } else {
            $this->whatsappService->sendEvolutionMessage(
                $conversation->external_id,
                $conversation->remote_jid,
                $request->content
            );
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    // ──────────────────────────────────────
    //  WEBHOOK ENTRY POINT
    // ──────────────────────────────────────

    public function handleWebhook(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verifyMetaWebhook($request);
        }

        $payload = $request->all();

        if (isset($payload['event']) && $payload['event'] === 'messages.upsert') {
            return $this->processEvolutionMessage($payload);
        }

        if (isset($payload['object']) && $payload['object'] === 'whatsapp_business_account') {
            return $this->processMetaMessage($payload);
        }

        return response()->json(['status' => 'unknown']);
    }

    protected function verifyMetaWebhook(Request $request)
    {
        $verifyToken = env('META_VERIFY_TOKEN', 'prexup_secret_token');

        if ($request->get('hub_verify_token') === $verifyToken) {
            return response($request->get('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    // ──────────────────────────────────────
    //  EVOLUTION API PROCESSOR
    // ──────────────────────────────────────

    protected function processEvolutionMessage($payload)
    {
        $data = $payload['data'];
        if ($data['key']['fromMe']) {
            return response()->json(['status' => 'ignored']);
        }

        $remoteJid = $data['key']['remoteJid'];
        $content = $data['message']['conversation']
            ?? $data['message']['extendedTextMessage']['text']
            ?? '';

        $conversation = Conversation::firstOrCreate(
            ['remote_jid' => $remoteJid],
            ['external_id' => $payload['instance']]
        );

        // ── Auto-link to Krayin Lead ──
        $this->leadSyncService->syncConversationToLead($conversation, [
            'lead_name' => $data['pushName'] ?? null,
        ]);
        $conversation->refresh();

        Message::create([
            'conversation_id' => $conversation->id,
            'content'         => $content,
            'sender'          => 'user',
        ]);

        // ── AI response ──
        $aiResponse = $this->aiService->getResponse($content, [
            'lead_id'   => $conversation->lead_id,
            'lead_name' => $data['pushName'] ?? null,
        ]);

        $this->whatsappService->sendEvolutionMessage(
            $payload['instance'],
            $remoteJid,
            $aiResponse
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'content'         => $aiResponse,
            'sender'          => 'bot',
        ]);

        // ── Qualify the lead after every user message ──
        $this->qualificationService->qualifyConversation($conversation);

        return response()->json(['status' => 'success']);
    }

    // ──────────────────────────────────────
    //  META CLOUD API PROCESSOR
    // ──────────────────────────────────────

    protected function processMetaMessage($payload)
    {
        $value = $payload['entry'][0]['changes'][0]['value'];

        if (!isset($value['messages'][0])) {
            return response()->json(['status' => 'no_msg']);
        }

        $msg     = $value['messages'][0];
        $from    = $msg['from'];
        $content = $msg['text']['body'] ?? '';
        $phoneId = $value['metadata']['phone_number_id'];

        $conversation = Conversation::firstOrCreate(
            ['remote_jid' => $from],
            ['external_id' => $phoneId]
        );

        // ── Auto-link to Krayin Lead ──
        $contactName = $value['contacts'][0]['profile']['name'] ?? null;
        $this->leadSyncService->syncConversationToLead($conversation, [
            'lead_name' => $contactName,
        ]);
        $conversation->refresh();

        Message::create([
            'conversation_id' => $conversation->id,
            'content'         => $content,
            'sender'          => 'user',
        ]);

        $aiResponse = $this->aiService->getResponse($content, [
            'lead_id' => $conversation->lead_id,
        ]);

        $this->whatsappService->sendMetaMessage($from, $aiResponse);

        Message::create([
            'conversation_id' => $conversation->id,
            'content'         => $aiResponse,
            'sender'          => 'bot',
        ]);

        // ── Qualify the lead after every user message ──
        $this->qualificationService->qualifyConversation($conversation);

        return response()->json(['status' => 'success']);
    }
}

