<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartProducts extends Model
{
    protected $table = 'tb_cartsproducts';
    protected $primaryKey = 'idcartproduct';

    protected $fillable = [
        'idcart',
        'idproduct',
        'qtd',
        'dtremoved',
        'dtregister'
    ];

    public $timestamps = false;

    public function cart()
    {
        return $this->belongsTo(CartProducts::class, 'idcart', 'idcart');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'idproduct', 'idproduct');
    }
}