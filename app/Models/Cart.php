<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    const SESSION = "Cart";

    protected $table = 'tb_carts';
    protected $primaryKey = 'idcart';

    protected $fillable = [
        'dessessionid',
        'iduser',
        'deszipcode',
        'vlfreight',
        'nrdays',
        'dtregister'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    public function cartProduct()
    {
        return $this->hasMany(CartProducts::class);
    }
}