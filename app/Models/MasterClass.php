<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $title
 * @property int $category_id
 * @property int $user_id
 * @property string $date
 * @property int $max_participants
 * ...
 */
class MasterClass extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'date',
        'time',
        'duration',
        'max_participants',
        'price',
    ];
    public const array TIME_SLOTS = ['09:00', '11:00', '13:00', '15:00'];
    protected $casts = [
        'date' => 'date',
        //'time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
