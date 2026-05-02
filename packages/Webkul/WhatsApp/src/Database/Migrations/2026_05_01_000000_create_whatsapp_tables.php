<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_conversations', function (Blueprint $chunk) {
            $chunk->id();
            $chunk->integer('lead_id')->unsigned()->nullable();
            $chunk->string('remote_jid');
            $chunk->string('external_id')->nullable(); // Instance name or Meta Phone ID
            $chunk->timestamps();

            $chunk->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
        });

        Schema::create('whatsapp_messages', function (Blueprint $chunk) {
            $chunk->id();
            $chunk->foreignId('conversation_id')->constrained('whatsapp_conversations')->onDelete('cascade');
            $chunk->text('content');
            $chunk->enum('sender', ['user', 'bot'])->default('user');
            $chunk->string('message_type')->default('text');
            $chunk->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
