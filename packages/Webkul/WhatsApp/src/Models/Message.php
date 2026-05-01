<?php

namespace Webkul\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = ['conversation_id', 'content', 'sender', 'message_type'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
