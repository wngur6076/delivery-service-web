<?php

namespace App\Models;

use App\Exceptions\EaterySyncException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getCart($eateryId)
    {
        if (isset($this->cart)) {
            if ($this->cart->eatery_id != $eateryId) {
                throw new EaterySyncException;
            } else {
                $cart = $this->cart;
            }
        } else {
            $cart = $this->cart()->create(['eatery_id' => $eateryId]);
        }

        return $cart;
    }
}
