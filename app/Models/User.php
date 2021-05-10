<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'tb_users';
    protected $primaryKey = 'iduser';

    public $timestamps = false;

    protected $fillable = [
        'idperson',
        'deslogin',
        'despassword',
        'inadmin'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'idperson', 'idperson');
    }


    public function cart()
    {
        return $this->hasOne(Cart::class, 'iduser', 'iduser');
    }
}