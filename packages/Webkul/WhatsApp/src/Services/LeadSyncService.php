<?php

namespace Webkul\WhatsApp\Services;

use Webkul\WhatsApp\Models\Conversation;
use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Pipeline;
use Illuminate\Support\Facades\Log;

class LeadSyncService
{
    /**
     * Try to link a WhatsApp conversation to an existing Krayin Lead.
     * If no Lead exists, optionally create one.
     */
    public function syncConversationToLead(Conversation $conversation, array $context = []): ?Lead
    {
        // Already linked
        if ($conversation->lead_id) {
            return Lead::find($conversation->lead_id);
        }

        $phone = $this->normalizePhone($conversation->remote_jid);

        // 1. Search for a Person with this phone number
        $person = $this->findPersonByPhone($phone);

        if ($person) {
            // 2. Check if person already has a lead
            $existingLead = $person->leads()->latest()->first();

            if ($existingLead) {
                $conversation->update(['lead_id' => $existingLead->id]);
                Log::info("WhatsApp conversation linked to existing lead #{$existingLead->id}");
                return $existingLead;
            }
        }

        // 3. Auto-create a new Lead + Person if configured
        if (env('WHATSAPP_AUTO_CREATE_LEADS', true)) {
            return $this->createLeadFromConversation($conversation, $person, $context);
        }

        return null;
    }

    /**
     * Create a new Lead (and Person if needed) from a WhatsApp conversation.
     */
    protected function createLeadFromConversation(Conversation $conversation, ?Person $person, array $context): Lead
    {
        $phone = $this->normalizePhone($conversation->remote_jid);

        // Create Person if not exists
        if (!$person) {
            $person = Person::create([
                'name'            => $context['lead_name'] ?? "WhatsApp {$phone}",
                'contact_numbers' => [['value' => $phone, 'label' => 'work']],
                'emails'          => [],
            ]);
            Log::info("Auto-created Person #{$person->id} from WhatsApp: {$phone}");
        }

        // Find the WhatsApp source (or default)
        $source = Source::where('name', 'like', '%whatsapp%')->first()
            ?? Source::first();

        // Use the default pipeline
        $pipeline = Pipeline::where('is_default', true)->first()
            ?? Pipeline::first();

        $firstStage = $pipeline?->stages()->orderBy('sort_order')->first();

        // Create the Lead
        $lead = Lead::create([
            'title'                  => "Lead WhatsApp — {$person->name}",
            'description'            => "Lead auto-generado desde conversación de WhatsApp. Teléfono: {$phone}",
            'lead_value'             => 0,
            'status'                 => 1, // Open
            'person_id'              => $person->id,
            'lead_source_id'         => $source?->id,
            'lead_pipeline_id'       => $pipeline?->id,
            'lead_pipeline_stage_id' => $firstStage?->id,
            'user_id'                => 1, // Default admin user
        ]);

        // Link conversation to the new lead
        $conversation->update(['lead_id' => $lead->id]);

        Log::info("Auto-created Lead #{$lead->id} from WhatsApp conversation #{$conversation->id}");

        return $lead;
    }

    /**
     * Search for a Person by phone number across the JSON contact_numbers field.
     */
    protected function findPersonByPhone(string $phone): ?Person
    {
        // Search in the JSON array field `contact_numbers`
        // Each entry is like: [{"value": "+34600000001", "label": "work"}]
        return Person::get()->first(function ($person) use ($phone) {
            if (!is_array($person->contact_numbers)) {
                return false;
            }

            foreach ($person->contact_numbers as $entry) {
                $stored = $this->normalizePhone($entry['value'] ?? '');
                if ($stored === $phone) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Normalize phone numbers for comparison.
     * Strips everything except digits.
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove @s.whatsapp.net suffix from Evolution API JIDs
        $phone = preg_replace('/@.*$/', '', $phone);

        // Keep only digits
        return preg_replace('/\D/', '', $phone);
    }
}
