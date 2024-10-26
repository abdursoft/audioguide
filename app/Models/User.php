<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'otp',
        'social_id',
        'customer_id',
        'social_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function UserSubscription(){
        return $this->hasMany(UserSubscription::class);
    }

    public function UserGuide(){
        return $this->hasMany(UserGuide::class);
    }

    public function UserBilling(){
        return $this->hasOne(UserBilling::class);
    }

    public function UserShipping(){
        return $this->hasOne(UserShipping::class);
    }

    public function Profile(){
        return $this->hasOne(Profile::class);
    }

    public function ProductReview(){
        return $this->hasMany(ProductReview::class);
    }
}
