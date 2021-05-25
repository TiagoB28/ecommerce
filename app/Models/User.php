<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'tb_users';
    protected $primaryKey = 'iduser';

    protected $fillable = [
        'idperson',
        'deslogin',
        'despassword',
        'inadmin'
    ];

    public $timestamps = false;

    public function person()
    {
        return $this->belongsTo(Person::class, 'idperson', 'idperson');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'iduser', 'iduser');
    }

    public function passwordRecovery()
    {
        return $this->hasOne(UserPasswordRecovery::class, 'iduser', 'iduser');
    }
}