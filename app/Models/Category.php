<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 */
class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'image', 'background_image'];

    public function masterClasses(): HasMany
    {
        return $this->hasMany(MasterClass::class);
    }
}
