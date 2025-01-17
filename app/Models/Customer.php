<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'status',
        'additional_details',
        'group_id'
    ];

    protected $casts = [
        'additional_details' => 'array',
        'status' => 'boolean'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
