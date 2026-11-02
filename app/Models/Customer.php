<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

    protected static function booted(): void
    {
        static::deleting(function (Customer $customer) {
            if ($customer->orders()->exists()) {
                throw new \DomainException('This customer is referenced by existing orders and cannot be deleted.');
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
