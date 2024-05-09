<?php

namespace App\Http\Controllers;

use App\OrdenCompra;
use App\Proyecto;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    public function getReporteProyecto($id, $detallado = 0){
        try{
            $proyecto = Proyecto::findOrFail($id);
            $ocs = OrdenCompra::where('proyecto_id', $proyecto->id)->where('rev_activa', true)->where('estado', '!=', -1)->with('proveedor')->get();
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
            $activeWorksheet->setTitle('Reporte');

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
            $drawing->setName('Logo Joremet');
            $drawing->setPath(public_path('logo_joremet.png'));
            $drawing->setHeight(48);
            $activeWorksheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);

            $activeWorksheet->getHeaderFooter()->setOddHeader('&L&G&RTipo Reporte: '.(($detallado==0)?'Resúmen': 'Detallado').'      Fecha Reporte: &D &T');
            $activeWorksheet->getHeaderFooter()->setOddFooter('&L&BProyecto ' . strtoupper($proyecto->nombre) . '&RPágina &P de &N');
            if($detallado == 0){
                $activeWorksheet->mergeCells('B2:F2');
                $activeWorksheet->mergeCells('B3:F3');
                $activeWorksheet->setCellValue('B2', 'Reporte de Proyecto - ' . $proyecto->nombre);
                $activeWorksheet->getStyle('B2')->getFont()->setBold( true );
                $activeWorksheet->getStyle('B2')->getAlignment()->setHorizontal('center');
                $activeWorksheet->getStyle('B2')->getFont()->setSize(18);
                $activeWorksheet->setCellValue('B3', 'Ordenes de Compra Asociadas');
                $activeWorksheet->getStyle('B3')->getAlignment()->setHorizontal('center');
                $activeWorksheet->getStyle('B3')->getFont()->setSize(18);
                $inicial = 5;
                $startIndex = $inicial;

                $activeWorksheet->setCellValue('B'.$startIndex, 'Folio');
                $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('B'.$startIndex)->getAlignment()->setHorizontal('center');
                $activeWorksheet->getStyle('B'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('C'.$startIndex, 'Razón Social');
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('C'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('D'.$startIndex, 'Fecha Emisión');
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal('center');
                $activeWorksheet->getStyle('D'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('E'.$startIndex, 'Monto Total');
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('E'.$startIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $activeWorksheet->getStyle('E'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $startIndex++;

                foreach($ocs as $oc){
                    $activeWorksheet->setCellValue('B'.$startIndex, $oc->folio);
                    $activeWorksheet->setCellValue('C'.$startIndex, $oc->proveedor->razon_social);
                    $activeWorksheet->setCellValue('D'.$startIndex, date('d-m-Y', strtotime($oc->fecha_emision)));
                    $activeWorksheet->setCellValue('E'.$startIndex, $oc->monto_total);
                    $activeWorksheet->getStyle('E'.$startIndex)->getNumberFormat()->setFormatCode('$ #,###0_-');

                    $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('B'.$startIndex)->getAlignment()->setHorizontal('center');
                    $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal('center');
                    $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);
                    $startIndex++;
                }
                $activeWorksheet->getCell('E'.$startIndex)->setValueExplicit('=SUM(E'.$inicial.':E'.($startIndex-1).')', DataType::TYPE_FORMULA);
                //$activeWorksheet->setCellValue('E'.$startIndex, '=SUMA(E'.$inicial.':E'.($startIndex-1).')');
                $activeWorksheet->getStyle('E'.$startIndex)->getNumberFormat()->setFormatCode('$ #,###0_-');
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);

                $activeWorksheet->getPageSetup()->setFitToWidth(1);
                $activeWorksheet->getPageSetup()->setFitToHeight(0);
                foreach (range('B', 'E') as $col) {
                    $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
                }

            }else if($detallado == 1){
                $activeWorksheet->mergeCells('B2:F2');
                $activeWorksheet->setCellValue('B2', 'Reporte de Proyecto - ' . $proyecto->nombre);

            }
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="sgjapp_'.preg_replace('/\s+/', '', strtolower($proyecto->nombre)).'.xlsx"'); /*-- $filename is  xsl filename ---*/
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
        }catch(Exception $ex){
            return $ex;
            return response()->json([
                'success' => false,
                'msg' => 'El reporte no pudo ser generado, comuniquese con soporte.',
            ]);
        }

    }
}
