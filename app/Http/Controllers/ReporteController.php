<?php

namespace App\Http\Controllers;

use App\Factura;
use App\GuiaDespacho;
use App\NotaCredito;
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
    public function getReporteProyecto($tipo, $id, $detallado = 0){
        try{
            $proyecto = Proyecto::findOrFail($id);
            $docs = null;
            $titleText = 'Documentos';
            switch($tipo){
                case 33:
                    $titleText = 'Facturas';
                    $docs = Factura::where('proyecto_id', $proyecto->id)->with('cliente')->get();
                    break;
                case 52:
                    $titleText = 'Guias de Despacho';
                    $docs = GuiaDespacho::where('proyecto_id', $proyecto->id)->with('cliente')->get();
                    break;
                case 61:
                    $titleText = 'Notas de Credito';
                    $docs = NotaCredito::where('proyecto_id', $proyecto->id)->with('cliente')->get();
                    break;
                case 0:
                    $titleText = 'Ordenes de Compra';
                    $docs = OrdenCompra::where('proyecto_id', $proyecto->id)->where('rev_activa', true)->where('estado', '!=', -1)->with('proveedor')->get();
                    break;
            }
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
            $activeWorksheet->setTitle('Reporte');

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
            $drawing->setName('Logo Joremet');
            $drawing->setPath(public_path('logo_joremet.png'));
            $drawing->setHeight(48);
            $activeWorksheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_LEFT);

            $activeWorksheet->getHeaderFooter()->setOddHeader('&16&L&G&RTipo Reporte: '.(($detallado==0)?'Resúmen': 'Detallado').'      Fecha Reporte: &D &T');
            $activeWorksheet->getHeaderFooter()->setOddFooter('&16&L&BProyecto ' . strtoupper($proyecto->nombre) . '&RPágina &P de &N');
            if($detallado == 0){
                $activeWorksheet->mergeCells('B2:F2');
                $activeWorksheet->mergeCells('B3:F3');
                $activeWorksheet->setCellValue('B2', 'Reporte de Proyecto - ' . $proyecto->nombre);
                $activeWorksheet->getStyle('B2')->getFont()->setBold( true );
                $activeWorksheet->getStyle('B2')->getAlignment()->setHorizontal('center');
                $activeWorksheet->getStyle('B2')->getFont()->setSize(18);
                $activeWorksheet->setCellValue('B3', $titleText.' Asociadas');
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

                foreach($docs as $doc){
                    $activeWorksheet->setCellValue('B'.$startIndex, $doc->folio);
                    $activeWorksheet->setCellValue('C'.$startIndex, ($tipo==0)?$doc->proveedor->razon_social:$doc->cliente->razon_social);
                    $activeWorksheet->setCellValue('D'.$startIndex, date('d-m-Y', strtotime($doc->fecha_emision)));
                    $activeWorksheet->setCellValue('E'.$startIndex, $doc->monto_total);
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
                $activeWorksheet->setCellValue('C'.$startIndex, 'Razón Social');
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->setCellValue('D'.$startIndex, 'Fecha Emisión');
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal('center');
                $activeWorksheet->setCellValue('E'.$startIndex, 'Monto Total');
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('E'.$startIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $startIndex++;
                $activeWorksheet->getStyle('B'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('C'.$startIndex, 'Item');
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('C'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('D'.$startIndex, 'Cantidad');
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $activeWorksheet->getStyle('D'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $activeWorksheet->setCellValue('E'.$startIndex, 'Subtotal');
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);
                $activeWorksheet->getStyle('E'.$startIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $activeWorksheet->getStyle('E'.$startIndex)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                $startIndex++;
                $celdasTotales = [];
                foreach($docs as $doc){
                    $activeWorksheet->setCellValue('B'.$startIndex, $doc->folio);
                    $activeWorksheet->setCellValue('C'.$startIndex, $doc->proveedor->razon_social);
                    $activeWorksheet->setCellValue('D'.$startIndex, date('d-m-Y', strtotime($doc->fecha_emision)));
                    $activeWorksheet->setCellValue('E'.$startIndex, $doc->monto_total);
                    $activeWorksheet->getStyle('E'.$startIndex)->getNumberFormat()->setFormatCode('$ #,###0_-');
                    array_push($celdasTotales, 'E'.$startIndex);
                    $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('B'.$startIndex)->getAlignment()->setHorizontal('center');
                    $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(16);
                    $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal('center');
                    $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);
                    $startIndex++;

                    foreach($doc->lineas as $linea){
                        $activeWorksheet->setCellValue('C'.$startIndex, $linea->nombre );
                        $activeWorksheet->setCellValue('D'.$startIndex, $linea->cantidad.' '.$linea->unidad);
                        $activeWorksheet->setCellValue('E'.$startIndex, ($linea->precio_unitario*$linea->cantidad)*1.19);
                        $activeWorksheet->getStyle('E'.$startIndex)->getNumberFormat()->setFormatCode('$ #,###0_-');
                        $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setItalic( true );
                        $activeWorksheet->getStyle('B'.$startIndex)->getFont()->setSize(14);
                        $activeWorksheet->getStyle('B'.$startIndex)->getAlignment()->setHorizontal('center');
                        $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setSize(14);
                        $activeWorksheet->getStyle('C'.$startIndex)->getFont()->setItalic( true );
                        $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setSize(14);
                        $activeWorksheet->getStyle('D'.$startIndex)->getFont()->setItalic( true );
                        $activeWorksheet->getStyle('D'.$startIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(14);
                        $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setItalic( true );
                        $startIndex++;
                    }
                    $activeWorksheet->getStyle('B'.$startIndex-1)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                    $activeWorksheet->getStyle('C'.$startIndex-1)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                    $activeWorksheet->getStyle('D'.$startIndex-1)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                    $activeWorksheet->getStyle('E'.$startIndex-1)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('DCDCDC'));
                }
                $activeWorksheet->getCell('E'.$startIndex)->setValueExplicit('='.implode('+', $celdasTotales), DataType::TYPE_FORMULA);
                //$activeWorksheet->setCellValue('E'.$startIndex, '=SUMA(E'.$inicial.':E'.($startIndex-1).')');
                $activeWorksheet->getStyle('E'.$startIndex)->getNumberFormat()->setFormatCode('$ #,###0_-');
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setBold( true );
                $activeWorksheet->getStyle('E'.$startIndex)->getFont()->setSize(16);

                $activeWorksheet->getPageSetup()->setFitToWidth(1);
                $activeWorksheet->getPageSetup()->setFitToHeight(0);
                foreach (range('B', 'E') as $col) {
                    $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
                }

            }
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="sgjapp_'.preg_replace('/\s+/', '', strtolower($proyecto->nombre)).'_'.$tipo.'.xlsx"'); /*-- $filename is  xsl filename ---*/
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
