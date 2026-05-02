<?php

namespace Webkul\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Lead\Models\LeadProxy;

class Conversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'lead_id',
        'remote_jid',
        'external_id',
        'ai_score',
        'intent_level',
        'budget_detected',
        'location_interest',
        'property_type',
        'qualification_summary',
        'qualified_at',
    ];

    protected $casts = [
        'qualified_at' => 'datetime',
        'ai_score' => 'integer',
    ];

    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Scope: only hot leads
     */
    public function scopeHotLeads($query)
    {
        return $query->where('intent_level', 'hot')
            ->orWhere('intent_level', 'ready_to_buy');
    }

    /**
     * Scope: order by qualification score
     */
    public function scopeByScore($query)
    {
        return $query->orderByDesc('ai_score');
    }
}
