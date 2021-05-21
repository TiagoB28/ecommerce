<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'tb_products';
    protected $primaryKey = 'idproduct';

    protected $fillable = [
        'desproduct',
        'vlprice',
        'vlwidth',
        'vlheight',
        'vllength',
        'vlweight',
        'desurl',
        'dtregister',
        'desimage'
    ];

    public $timestamps = false;

    public function categories()
    {
        return $this->belongsToMany(Category::class,
            'tb_productscategories','idproduct', 'idcategory');
    }

    /*public function cartProduct()
    {
        return $this->hasMany(CartProducts::class);
    }*/
}