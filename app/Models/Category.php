<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            if ($category->products()->exists()) {
                throw new \DomainException('This category still has products and cannot be deleted.');
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
