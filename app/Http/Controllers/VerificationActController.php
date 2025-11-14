<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class VerificationActController extends Controller
{
    public function exportVerificationAct()
    {
        try {
            $templatePath = storage_path('app/public/plantillas/Acta_de_verificacion.xlsx');

            if (!file_exists($templatePath)) {
                return redirect()->route('risks.matrix')
                    ->with('error', "No se encontró la plantilla en: $templatePath");
            }

            $riesgos = Risk::orderBy('lugar')
                ->orderBy('peligro')
                ->get();

            if ($riesgos->isEmpty()) {
                return redirect()->route('risks.matrix')
                    ->with('error', "No hay riesgos registrados para exportar");
            }

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $filaInicio = 16;
            $filaActual = $filaInicio;
            $numero = 1;

            $agrupados = $riesgos->groupBy('lugar');

            foreach ($agrupados as $lugar => $peligros) {
                $inicioLugar = $filaActual;

                foreach ($peligros as $risk) {
                    if ($filaActual > $filaInicio) {
                        $sheet->insertNewRowBefore($filaActual, 1);
                    }

                    // Número (B–C)
                    $sheet->mergeCells("B{$filaActual}:C{$filaActual}");
                    $sheet->setCellValue("B{$filaActual}", $numero++);
                    $sheet->getStyle("B{$filaActual}:C{$filaActual}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    // Peligro (H–AM)
                    $sheet->mergeCells("H{$filaActual}:AM{$filaActual}");
                    $sheet->setCellValue("H{$filaActual}", $risk->peligro);
                    $sheet->getStyle("H{$filaActual}:AM{$filaActual}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $filaActual++;
                }

                $finLugar = $filaActual - 1;

                // Lugar (D–G)
                $sheet->mergeCells("D{$inicioLugar}:G{$finLugar}");
                $sheet->setCellValue("D{$inicioLugar}", $lugar);
                $sheet->getStyle("D{$inicioLugar}:G{$finLugar}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Mes en español
            Carbon::setLocale('es');
            $fecha = Carbon::now();
            $mes = ucfirst($fecha->translatedFormat('F'));

            $nombreArchivo = "Acta_de_verificacion_{$mes}_{$fecha->year}.xlsx";

            // Configurar headers para descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            \Log::error('Error al exportar acta de verificación: ' . $e->getMessage());
            return redirect()->route('risks.matrix')
                ->with('error', 'Error al generar el archivo: ' . $e->getMessage());
        }
    }
}