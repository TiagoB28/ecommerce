<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'tb_categories';
    protected $primaryKey = 'idcategory';

    protected $fillable = [
        'descategory',
        'dtregister'
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class,
            'tb_productscategories', 'idcategory', 'idproduct');
    }
}