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
        'idaddress',
        'vlfreight',
        'dtregister'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }
}