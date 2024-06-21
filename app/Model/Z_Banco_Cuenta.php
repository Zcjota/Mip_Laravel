<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Z_Banco_Cuenta extends Model
{
    protected $table = 'z_banco_cuenta';
    protected $primaryKey = 'cod_bc';
    public $timestamps = false;
    
}
