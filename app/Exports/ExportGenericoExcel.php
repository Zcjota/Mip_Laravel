<?php
namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExportGenericoExcel  implements FromQuery, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;

    public function __construct(Request $request, $query)
    {
        $this->query = $query;
        // Aquí puedes agregar lógica adicional para procesar la solicitud, si es necesario
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        // Aquí puedes definir los encabezados del archivo Excel
        return [];
    }

    public function map($row): array
    {
        // Aquí puedes mapear cada fila de datos según tus necesidades
        return [];
    }
}
