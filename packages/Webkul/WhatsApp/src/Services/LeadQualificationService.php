<?php

namespace Webkul\WhatsApp\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Webkul\WhatsApp\Models\Conversation;
use Webkul\WhatsApp\Models\Message;

class LeadQualificationService
{
    // ──────────────────────────────────────
    //  KEYWORD-BASED SCORING (Offline/Fast)
    // ──────────────────────────────────────

    /**
     * High-intent keywords that indicate a lead is close to buying.
     */
    protected $highIntentKeywords = [
        'comprar' => 15,
        'visitar' => 12,
        'agendar' => 12,
        'precio' => 10,
        'cuánto' => 10,
        'cuanto' => 10,
        'financiamiento' => 8,
        'crédito' => 8,
        'credito' => 8,
        'hipoteca' => 8,
        'mudarnos' => 12,
        'mudanza' => 10,
        'urgente' => 15,
        'disponible' => 8,
        'reservar' => 15,
        'reserva' => 15,
        'señal' => 15,
        'contrato' => 18,
        'escritura' => 18,
        'notaría' => 18,
        'terraza' => 5,
        'garaje' => 5,
        'habitaciones' => 5,
        'metros' => 5,
        'zona' => 5,
    ];

    /**
     * Budget patterns to detect price ranges.
     */
    protected $budgetPatterns = [
        '/(\d{2,3})[.\s]?(\d{3})\s*€/',            // 250.000€ or 250 000€
        '/(\d{2,3})k\s*€?/i',                        // 250k€ or 250k
        '/presupuesto.*?(\d{2,3})[.\s]?(\d{3})/i',   // presupuesto de 250.000
        '/entre\s*(\d+).*?y\s*(\d+)/i',               // entre 200 y 350
        '/hasta\s*(\d{2,3})[.\s]?(\d{3})/i',          // hasta 300.000
    ];

    /**
     * Location keywords for Spanish real estate.
     */
    protected $locationKeywords = [
        'gràcia', 'eixample', 'sant gervasi', 'sarrià', 'barceloneta',
        'poblenou', 'born', 'gótico', 'raval', 'horta', 'sants',
        'les corts', 'pedralbes', 'tibidabo', 'diagonal', 'marina',
        'centro', 'playa', 'montaña', 'campo', 'costa', 'ciudad',
        'miraflores', 'san isidro', 'surco', 'la molina', 'barranco',
    ];

    /**
     * Property type detection.
     */
    protected $propertyTypes = [
        'piso', 'apartamento', 'ático', 'atico', 'casa', 'chalet',
        'villa', 'local', 'oficina', 'terreno', 'lote', 'duplex',
        'estudio', 'loft', 'penthouse', 'finca',
    ];

    // ──────────────────────────────────────
    //  MAIN QUALIFICATION METHOD
    // ──────────────────────────────────────

    /**
     * Analyze all messages in a conversation and produce a qualification.
     */
    public function qualifyConversation(Conversation $conversation): Conversation
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->where('sender', 'user')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            return $conversation;
        }

        // Concatenate all user messages for analysis
        $fullText = $messages->pluck('content')->implode(' ');
        $fullTextLower = mb_strtolower($fullText);

        // 1. KEYWORD SCORING
        $score = $this->calculateKeywordScore($fullTextLower);

        // 2. ENGAGEMENT BONUS (more messages = more engaged)
        $messageCount = $messages->count();
        $engagementBonus = min($messageCount * 3, 20); // max +20
        $score += $engagementBonus;

        // 3. RECENCY BONUS (contacted in last 24h)
        $lastMessage = $messages->last();
        if ($lastMessage && $lastMessage->created_at->isToday()) {
            $score += 5;
        }

        // 4. DETECT BUDGET
        $budget = $this->detectBudget($fullTextLower);

        // 5. DETECT LOCATION
        $location = $this->detectLocation($fullTextLower);

        // 6. DETECT PROPERTY TYPE
        $propertyType = $this->detectPropertyType($fullTextLower);

        // 7. Cap score at 100
        $score = min($score, 100);

        // 8. Determine intent level
        $intentLevel = $this->scoreToIntent($score);

        // 9. Generate summary
        $summary = $this->generateSummary($conversation, $score, $intentLevel, $budget, $location, $propertyType, $messageCount);

        // 10. Update conversation
        $conversation->update([
            'ai_score' => $score,
            'intent_level' => $intentLevel,
            'budget_detected' => $budget,
            'location_interest' => $location,
            'property_type' => $propertyType,
            'qualification_summary' => $summary,
            'qualified_at' => now(),
        ]);

        Log::info("Lead qualified: {$conversation->remote_jid} → Score: {$score} ({$intentLevel})");

        // 11. If n8n webhook is set, also send for deeper AI analysis
        $this->sendToN8nForDeepAnalysis($conversation, $fullText);

        return $conversation->fresh();
    }

    // ──────────────────────────────────────
    //  SCORING ENGINE
    // ──────────────────────────────────────

    protected function calculateKeywordScore(string $text): int
    {
        $score = 0;

        foreach ($this->highIntentKeywords as $keyword => $points) {
            if (str_contains($text, $keyword)) {
                $score += $points;
            }
        }

        return $score;
    }

    protected function detectBudget(string $text): ?string
    {
        foreach ($this->budgetPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    protected function detectLocation(string $text): ?string
    {
        $found = [];

        foreach ($this->locationKeywords as $location) {
            if (str_contains($text, $location)) {
                $found[] = ucfirst($location);
            }
        }

        return ! empty($found) ? implode(', ', $found) : null;
    }

    protected function detectPropertyType(string $text): ?string
    {
        foreach ($this->propertyTypes as $type) {
            if (str_contains($text, $type)) {
                return ucfirst($type);
            }
        }

        return null;
    }

    protected function scoreToIntent(int $score): string
    {
        if ($score >= 80) {
            return 'ready_to_buy';
        }
        if ($score >= 55) {
            return 'hot';
        }
        if ($score >= 30) {
            return 'warm';
        }

        return 'cold';
    }

    // ──────────────────────────────────────
    //  SUMMARY GENERATOR
    // ──────────────────────────────────────

    protected function generateSummary(
        Conversation $conversation,
        int $score,
        string $intentLevel,
        ?string $budget,
        ?string $location,
        ?string $propertyType,
        int $messageCount
    ): string {
        $parts = [];

        $parts[] = "Lead con {$messageCount} mensajes.";

        if ($budget) {
            $parts[] = "Presupuesto detectado: {$budget}.";
        }

        if ($location) {
            $parts[] = "Interés en zona: {$location}.";
        }

        if ($propertyType) {
            $parts[] = "Busca: {$propertyType}.";
        }

        $intentLabels = [
            'cold' => 'Frío — solo explorando',
            'warm' => 'Tibio — mostrando interés',
            'hot' => 'Caliente — listo para visitar',
            'ready_to_buy' => '🔥 Listo para comprar',
        ];

        $parts[] = "Nivel: {$intentLabels[$intentLevel]}.";
        $parts[] = "Score: {$score}/100.";

        return implode(' ', $parts);
    }

    // ──────────────────────────────────────
    //  N8N DEEP ANALYSIS (Optional)
    // ──────────────────────────────────────

    /**
     * Send conversation data to n8n for deeper AI-powered analysis.
     * n8n can then update the conversation via API or trigger follow-up actions.
     */
    protected function sendToN8nForDeepAnalysis(Conversation $conversation, string $fullText): void
    {
        $webhookUrl = env('N8N_QUALIFICATION_WEBHOOK_URL');

        if (! $webhookUrl) {
            return; // n8n not configured, skip
        }

        try {
            $client = new Client;
            $client->post($webhookUrl, [
                'json' => [
                    'conversation_id' => $conversation->id,
                    'remote_jid' => $conversation->remote_jid,
                    'lead_id' => $conversation->lead_id,
                    'full_text' => $fullText,
                    'current_score' => $conversation->ai_score,
                    'intent_level' => $conversation->intent_level,
                    'budget' => $conversation->budget_detected,
                    'location' => $conversation->location_interest,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('n8n qualification webhook failed: '.$e->getMessage());
        }
    }
}
