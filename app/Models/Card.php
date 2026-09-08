<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;
    
    protected $fillable = [
        'card_number',
        'pin',
        'activation_date',
        'expiration_date',
        'balance',
    ];

    protected $casts = [
        'activation_date' => 'datetime',
        'expiration_date' => 'date',
        'balance' => 'decimal:2',
    ];
}
