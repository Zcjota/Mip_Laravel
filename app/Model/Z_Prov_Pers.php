<?php



namespace App\Model;



use Illuminate\Database\Eloquent\Model;



class Z_Prov_Pers extends Model

{

    protected $table = 'z_prov_pers';

    protected $primaryKey = 'id';

    

    protected $fillable = ['tipo','nombre_completo','fondo','nro_cuenta','cuenta','cod_bc','activo','elim'];

    public $timestamps = false;

}

