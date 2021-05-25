<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPasswordRecovery extends Model
{
    protected $table = 'tb_userspasswordsrecoveries';
    protected $primaryKey = 'idrecovery';

    protected $fillable = [
        'iduser',
        'desip',
        'dtrecovery',
        'dtregister'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }
}