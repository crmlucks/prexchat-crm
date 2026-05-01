<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add AI qualification columns to whatsapp_conversations.
     */
    public function up()
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->integer('ai_score')->default(0)->comment('0-100 qualification score');
            $table->enum('intent_level', ['cold', 'warm', 'hot', 'ready_to_buy'])->default('cold');
            $table->string('budget_detected')->nullable()->comment('e.g. 200k-350k');
            $table->string('location_interest')->nullable()->comment('e.g. Gràcia, Eixample');
            $table->string('property_type')->nullable()->comment('e.g. piso, ático, casa');
            $table->text('qualification_summary')->nullable()->comment('AI-generated summary');
            $table->timestamp('qualified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropColumn([
                'ai_score',
                'intent_level',
                'budget_detected',
                'location_interest',
                'property_type',
                'qualification_summary',
                'qualified_at',
            ]);
        });
    }
};
