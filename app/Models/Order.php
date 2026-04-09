<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'items', 'total', 'status', 'payment_proof', 'notes',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_payment'      => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'paid'                 => 'Lunas',
            'cancelled'            => 'Dibatalkan',
            default                => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending_payment'      => 'yellow',
            'waiting_confirmation' => 'blue',
            'paid'                 => 'green',
            'cancelled'            => 'red',
            default                => 'gray',
        };
    }
}
