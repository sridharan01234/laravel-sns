<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'variables',
        'msg91_template_id',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
        'status' => 'boolean'
    ];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
