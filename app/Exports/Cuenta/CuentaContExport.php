<?php

namespace App\Exports\Cuenta;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth si no está ya importado
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
use App\Model\Cuenta;

use Maatwebsite\Excel\Concerns\WithEvents;
class CuentaContExport implements FromCollection , WithHeadings, WithStyles, ShouldAutoSize, WithMapping, WithEvents
{
    
    protected $buscar;
    

   public function __construct($buscar = null)
    {
        $this->buscar = $buscar;
    }

    public function collection()
    {
        $cuenta_contable=Cuenta::leftjoin('fin_fondo AS c','Cuenta.COD_FONDO','=','c.COD_FONDO') 
            ->leftjoin('nro_cuenta AS b', 'cuenta.NRO_CUENTA', '=', 'b.CODNUM')
            ->select(
                'cuenta.COD_CUENTA as id','c.DESCRIPCION as nombre_fondo','b.DESCRIPCION as nombre_nro_cuenta',
                'cuenta.DESCRIPCION as nombre_cuenta','cuenta.ACTIVO as activo',
                'cuenta.COD_FONDO as cod_fondo','cuenta.NRO_CUENTA as cod_cuentas'
            )
            ->where('cuenta.DESCRIPCION', 'like', '%' . $this->buscar . '%')
                // ->orWhere('b.DESCRIPCION', 'like', '%' . $this->buscar . '%')
            ->orderBy('cuenta.COD_CUENTA','desc');

return $cuenta_contable->get();

    }

    public function headings(): array
    {
        return [
            'CODIGO',
            'FONDO',
            'NRO.CUENTA',
            'CUENTA PRESUPUESTO.',
            'ESTADO'
        ];
    }

    public function map($cuenta_contable): array
    {

        // $tipo = $cuenta_contable->tipo == 1 ? 'PERSONAL' : ($cuenta_contable->tipo == 2 ? 'PROVEEDOR' : 'DESCONOCIDO');

        // Devuelve los datos de cada fila en el formato que deseas exportar.
        return [
            $cuenta_contable->id,
            $cuenta_contable->nombre_fondo,
            $cuenta_contable->nombre_nro_cuenta,
            $cuenta_contable->nombre_cuenta,
            $cuenta_contable->activo ? 'ACTIVO' : 'INACTIVO',
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
    
    $sheet->getStyle('A1:G1')->applyFromArray([
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

    $sheet->getStyle('D2:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

}
}
