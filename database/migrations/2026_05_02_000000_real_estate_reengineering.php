<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── 1. INVENTARIO DE PROPIEDADES ──
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['house', 'apartment', 'land', 'office', 'commercial'])->default('house');
            $table->enum('status', ['available', 'reserved', 'sold', 'rented'])->default('available');
            $table->enum('listing_type', ['sale', 'rent', 'temporary'])->default('sale');
            
            $table->decimal('price', 15, 2);
            $table->string('currency')->default('USD');
            
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->integer('rooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('parking_spots')->default(0);
            
            $table->string('city');
            $table->string('address');
            $table->json('features')->nullable(); // Pool, Garden, Gym, etc.
            $table->json('images')->nullable();
            
            $table->timestamps();
        });

        // ── 2. PERFIL DETALLADO DE LEADS ──
        Schema::create('real_estate_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            
            // Perfil Económico Detectado por IA
            $table->decimal('min_budget', 15, 2)->nullable();
            $table->decimal('max_budget', 15, 2)->nullable();
            $table->json('preferred_locations')->nullable(); // Ciudades o Barrios
            $table->json('preferred_types')->nullable(); // Tipos de propiedad
            
            $table->integer('ai_score')->default(0); // 0-100 (Probabilidad de cierre)
            $table->enum('urgency', ['low', 'medium', 'high', 'immediate'])->default('low');
            
            $table->unsignedBigInteger('assigned_agent_id')->nullable();
            $table->timestamps();
        });

        // ── 3. PIPELINE DE VENTAS (HISTORIAL) ──
        Schema::create('sales_pipeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('real_estate_leads')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained('properties');
            $table->string('stage'); // Prospect, AI Qualified, Viewing, Offer, Closing
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_pipeline');
        Schema::dropIfExists('real_estate_leads');
        Schema::dropIfExists('properties');
    }
};
