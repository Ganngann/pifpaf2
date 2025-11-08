<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'image_path',
        'result',
        'error_message',
        'created_item_ids',
        'retry_count',
        'raw_error_response',
    ];

    protected $casts = [
        'result' => 'array',
        'created_item_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
