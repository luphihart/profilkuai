<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationMessage extends Model
{
    protected $table = 'conversation_messages';

    // Laravel 12 default timestamps bisa dimatikan jika kita hanya menggunakan created_at
    public $timestamps = false;

    protected $fillable = ['conversation_id', 'sender', 'message_text', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke Percakapan induk
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
