<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;

use App\Models\Producto;
use App\Models\Presentacion;
use Illuminate\Http\Request;

class ExcelController extends Controller
{   
public function index(){

    return view('excel.index');
}


public function update(Request $request){

    return redirect()->route('excel.index')->with('info','Información de la empresa actualizada con éxito');
}


public function export() {
    $productos = Producto::all();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Id');
    $sheet->setCellValue('B1', 'Nombre');
    $sheet->setCellValue('C1', 'Oferta');
    $sheet->setCellValue('D1', 'Mostrar');
    $sheet->setCellValue('E1' , 'Cantidad vendida');

    $sheet->setCellValue('F1' , 'Presentación');
    $sheet->setCellValue('G1' , 'Mostrar presentación');
    $sheet->setCellValue('H1' , 'Precio');
    $sheet->setCellValue('I1' , 'Precio anterior');
    $sheet->setCellValue('J1' , 'Metros');
    $sheet->setCellValue('K1' , 'Peso');
    $sheet->setCellValue('L1' , 'Límite de compra');
    $sheet->setCellValue('M1' , 'Stock');



    $rows = 2;
    foreach($productos as $producto){
    $presentaciones = $producto->presentaciones;

        if(count($presentaciones)  ){
            foreach($presentaciones as $presentacion){
            $sheet->setCellValue('A' . $rows, $producto->id);
            $sheet->setCellValue('B' . $rows, $producto->medidas->medidas . $producto->espesor->espesor);
            $sheet->setCellValue('C' . $rows, $producto->oferta);
            $sheet->setCellValue('D' . $rows, $producto->show);
            $sheet->setCellValue('E' . $rows, $producto->vendidos);

            $sheet->setCellValue('F' . $rows, $presentacion->nombre);
            $sheet->setCellValue('G' . $rows, $presentacion->show);
            $sheet->setCellValue('H' . $rows, $presentacion->precio);
            $sheet->setCellValue('I' . $rows, $presentacion->precio_anterior);
            $sheet->setCellValue('J' . $rows, $presentacion->metros);
            $sheet->setCellValue('K' . $rows, $presentacion->peso);

            $sheet->setCellValue('L' . $rows, $presentacion->limite);
            $sheet->setCellValue('M' . $rows, $presentacion->stock);




            $rows++;
            }
        }else{
            $sheet->setCellValue('A' . $rows, $producto->id);
            $sheet->setCellValue('B' . $rows, $producto->nombre);
            $sheet->setCellValue('C' . $rows, $producto->oferta); 
            $sheet->setCellValue('D' . $rows, $producto->show);
            $sheet->setCellValue('E' . $rows, $producto->vendidos);

            $rows++;
        }
    }
    $fileName = "emp.xlsx";
    
    $writer = new Xlsx($spreadsheet);
    
    $writer->save("export/".$fileName);
    header("Content-Type: application/vnd.ms-excel");
    return redirect(url('/')."/export/".$fileName);
}


public function import() {
    if(!request()->hasFile('archivo')){
        return redirect()->route('excel.index')->with('info','No ha ingresado ningún archivo');
    }
    $file = request()->file('archivo')->store('import');
    $reader = new ReaderXlsx();
    $spreadsheet = $reader->load(storage_path('app/'.$file));
    $sheet = $spreadsheet->getActiveSheet();

    $rows = $sheet->toArray();

    Presentacion::truncate();
    for ($i=1; $i < count($rows) ; $i++) { 
        
        $presentacion = new Presentacion;
    
        $producto = Producto::find($rows[$i][0]);

        $producto->oferta = $rows[$i][2];
        $producto->show = $rows[$i][3];
        $producto->vendidos = $rows[$i][4];

        $presentacion->producto_id = $rows[$i][0];
        $presentacion->nombre = $rows[$i][5];
        $presentacion->show = $rows[$i][6];
        $presentacion->precio = $rows[$i][7];
        $presentacion->precio_anterior = $rows[$i][8];
        $presentacion->metros = $rows[$i][9];
        $presentacion->peso = $rows[$i][10];

        $presentacion->limite = $rows[$i][11];
        $presentacion->stock = $rows[$i][12];




        $presentacion->save();
        $producto->save();


    }
    
    return redirect()->route('excel.index')->with('info','BD actualizada con éxito');
}

}
