<?php

// namespace App\Exports\Conciliacion\Reportes;
namespace App\Exports\PersonalProveedor;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth si no está ya importado
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Model\Z_Prov_Pers;

use Maatwebsite\Excel\Concerns\WithEvents;


// class PersonalExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
class PersonalExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping, WithEvents
{

    protected $buscar;
    

   public function __construct($buscar = null)
    {
        $this->buscar = $buscar;
    }

    public function collection()
    {
        $personal=Z_Prov_Pers::leftJoin('fin_fondo','z_prov_pers.fondo','=','fin_fondo.COD_FONDO') 
        ->leftJoin('nro_cuenta','z_prov_pers.nro_cuenta','=','nro_cuenta.CODNUM') 
        ->leftJoin('cuenta','z_prov_pers.cuenta','=','cuenta.COD_CUENTA')
        ->leftJoin('z_banco_cuenta as bc', 'z_prov_pers.cod_bc','=', 'bc.cod_bc')
        ->join('z_banco as bb','bc.cod_b','=','bb.cod_b')
        ->select(
            'z_prov_pers.id as id','z_prov_pers.tipo AS tipo',
            'z_prov_pers.nombre_completo as nombre',
            'z_prov_pers.fondo as fondo','z_prov_pers.nro_cuenta as nro_cuenta',
            'z_prov_pers.cuenta as cuenta','z_prov_pers.activo as activo',
            'fin_fondo.DESCRIPCION as nombre_fondo',
            'nro_cuenta.DESCRIPCION as nombre_nro_cuenta',
            'cuenta.DESCRIPCION as nombre_cuenta', 'z_prov_pers.cod_bc'
            , 'bb.sigla as b_sigla', 'bb.nombre as b_nombre'
            , 'bc.nro_cuenta as bc_cuenta', 'bc.moneda as bc_moneda'
        )
        ->where('z_prov_pers.nombre_completo', 'like', '%' . $this->buscar . '%')
            ->orWhere('bc.nro_cuenta', 'like', '%' . $this->buscar . '%')
        ->orderBy('z_prov_pers.id','asc');

return $personal->get();

    }

    public function headings(): array
    {
        return [
            'CODIGO',
            'TIPO',
            'NOMBRE COMPLETO',
            'FONDO',
            'NRO.CUENTA',
            'CUENTA PRESUPUESTO',
            'BANCO',
            'NRO.CUENTA BANCO',
            'MONEDA',
            'ESTADO'
        ];
    }

    public function map($personal): array
    {

        $tipo = $personal->tipo == 1 ? 'PERSONAL' : ($personal->tipo == 2 ? 'PROVEEDOR' : 'DESCONOCIDO');

        // Devuelve los datos de cada fila en el formato que deseas exportar.
        return [
            $personal->id,
            $tipo,
            $personal->nombre,
            $personal->nombre_fondo,
            $personal->nombre_nro_cuenta,
            $personal->nombre_cuenta,
            $personal->b_nombre,
            $personal->bc_cuenta,
            $personal->bc_moneda,
            $personal->activo ? 'ACTIVO' : 'INACTIVO',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aquí puedes manipular la hoja después de que se ha cargado.
                // Por ejemplo, aplicar estilos a ciertas celdas.
            },
        ];
    }


    public function styles(Worksheet $sheet)
{
    
    $sheet->getStyle('A1:J1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 11,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => '526D84'],
        ],
    ]);

    $sheet->getStyle('H2:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

}

}
