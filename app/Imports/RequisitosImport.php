<?php

namespace App\Imports;

use App\Models\RequisitoLegal;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;

class RequisitosImport implements OnEachRow, WithStartRow
{
    protected $categoriaActual = null;
    protected $rowCount = 0;

    public function startRow(): int
    {
        return 9;
    }

    public function onRow(Row $row)
    {
        $this->rowCount++;
        $rowIndex = $row->getIndex();
        $r = $row->toArray();

        if ($this->esFilaSeparadora($r, $rowIndex)) {
            $this->procesarFilaSeparadora($r);
            return;
        }

        if ($this->categoriaActual === null) {
            return;
        }

        if (!$this->esFilaValida($r)) {
            return;
        }

        $this->crearRequisito($r);
    }

    private function esFilaSeparadora($r, $rowIndex)
    {
        $columnaA = trim($r[0] ?? '');
        
        if (!empty($columnaA)) {
            $texto = strtoupper($columnaA);
            
            if (Str::contains($texto, 'NORMAS DE SEGURIDAD') || 
                Str::contains($texto, 'NORMAS DE SALUD') || 
                Str::contains($texto, 'NORMAS DE ORGANIZACIÓN')) {
                return true;
            }
        }

        return false;
    }

    private function procesarFilaSeparadora($r)
    {
        $texto = strtoupper(trim($r[0] ?? ''));

        if (Str::contains($texto, 'NORMAS DE SEGURIDAD')) {
            $this->categoriaActual = 'seguridad';
        } elseif (Str::contains($texto, 'NORMAS DE SALUD')) {
            $this->categoriaActual = 'salud';
        } elseif (Str::contains($texto, 'NORMAS DE ORGANIZACIÓN')) {
            $this->categoriaActual = 'organizacion';
        } else {
            $this->categoriaActual = null;
        }
    }

    private function esFilaValida($r)
    {
        $tieneNorma = !empty(trim($r[0] ?? ''));
        $tieneTitulo = !empty(trim($r[1] ?? ''));
        $tieneTipoRequisito = !empty(trim($r[2] ?? ''));
        
        return $tieneNorma && $tieneTitulo && $tieneTipoRequisito;
    }

    private function crearRequisito($r)
    {
        try {
            $data = [
                'categoria_norma'     => $this->categoriaActual,
                'norma'               => trim($r[0] ?? ''),
                'titulo'              => trim($r[1] ?? ''),
                'tipo_requisito'      => trim($r[2] ?? ''),
                'numero_requisito'    => $this->formatearNumeroRequisito($r[3] ?? ''),
                'descripcion'         => trim($r[4] ?? ''),
                'cumplimiento'        => $this->determinarCumplimiento($r),
                'evidencia'           => trim($r[6] ?? ''),
                'acciones_no'         => trim($r[8] ?? ''),
                'peligro_asociado'    => trim($r[9] ?? ''),
                'fecha_cumplimiento'  => $this->parsearFecha($r[10] ?? null),
                'responsables'        => trim($r[11] ?? ''),
                'frecuencia_control'  => trim($r[12] ?? ''),
                'responsable_control' => trim($r[13] ?? ''),
            ];

            $existe = RequisitoLegal::where('norma', $data['norma'])
                ->where('titulo', $data['titulo'])
                ->where('tipo_requisito', $data['tipo_requisito'])
                ->where('numero_requisito', $data['numero_requisito'])
                ->where('descripcion', $data['descripcion'])
                ->exists();

            if (!$existe) {
                RequisitoLegal::create($data);
            }

        } catch (\Exception $e) {
            \Log::error("Error creando requisito: " . $e->getMessage());
        }
    }

    private function formatearNumeroRequisito($valor)
    {
        if (empty($valor)) return '';

        $valor = trim($valor);

        if (is_numeric($valor)) {
            try {
                $fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);
                $dia = (int)$fechaObj->format('d');
                $mes = (int)$fechaObj->format('m');
                return $dia . '.' . $mes;
            } catch (\Exception $e) {
                return (string)$valor;
            }
        }

        return $valor;
    }

    private function determinarCumplimiento($r)
    {
        $columnaSI = trim($r[5] ?? '');
        $columnaNO = trim($r[7] ?? '');

        if (!empty($columnaSI) && Str::contains(strtoupper($columnaSI), ['X', 'SI', 'CUMPLIDO'])) {
            return 'si';
        }

        if (!empty($columnaNO) && Str::contains(strtoupper($columnaNO), ['X', 'NO', 'PENDIENTE'])) {
            return 'no';
        }

        return null;
    }

    private function parsearFecha($fecha)
    {
        if (empty($fecha)) return null;
        
        try {
            if (is_numeric($fecha)) {
                $fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha);
                return $fechaObj->format('Y-m-d');
            }
            
            $fechaObj = \Carbon\Carbon::parse($fecha);
            return $fechaObj->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}