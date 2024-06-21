<?php

namespace App;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $fillable = ['codPersonal','codRol','imagen','usuario','password','estado'];

    public $timestamps = false;

    protected $hidden = [
        'password', 'remember_token',
    ];

}
