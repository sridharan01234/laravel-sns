<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'customer_id',
        'status',
        'variables',
        'response',
        'sent_at'
    ];

    protected $casts = [
        'variables' => 'array',
        'response' => 'array',
        'sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
