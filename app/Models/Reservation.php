<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'court_id',
        'date',
        'time_slot',
        'hourly_rate',
        'status',
        'expires_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method_id',
        'payment_id',
        'payment_reference',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'hourly_rate' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
