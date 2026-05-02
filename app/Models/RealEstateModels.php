<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'status', 'listing_type', 
        'price', 'currency', 'area_m2', 'rooms', 'bathrooms', 
        'parking_spots', 'city', 'address', 'features', 'images'
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
    ];
}

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateLead extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email', 'min_budget', 'max_budget', 
        'preferred_locations', 'preferred_types', 'ai_score', 
        'urgency', 'assigned_agent_id'
    ];

    protected $casts = [
        'preferred_locations' => 'array',
        'preferred_types' => 'array',
        'min_budget' => 'decimal:2',
        'max_budget' => 'decimal:2',
    ];
}
