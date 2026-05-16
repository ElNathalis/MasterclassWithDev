<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory; 
    protected $fillable = ['user_id', 'master_class_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function masterClass(): BelongsTo
    {
        return $this->belongsTo(MasterClass::class);
    }
}
