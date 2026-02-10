<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;

use Illuminate\Http\Request;

class ExcelcpController extends Controller
{   
public function index(){

    return view('excelcp.index');
}


public function update(Request $request){

    return redirect()->route('excelcp.index')->with('info','Información de la empresa actualizada con éxito');
}


public function export() {
    $envios = Envio::all();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Id');
    $sheet->setCellValue('B1', 'Código Postal');
    $sheet->setCellValue('C1', 'Costo');

    $rows = 2;
    foreach($envios as $envio){
        $sheet->setCellValue('A' . $rows, $envio->id);
        $sheet->setCellValue('B' . $rows, $envio->cp);
        $sheet->setCellValue('C' . $rows, $envio->costo);

        $rows++;
    }

    $fileName = "emp.xlsx";
    
    $writer = new Xlsx($spreadsheet);
    
    $writer->save("export/".$fileName);
    header("Content-Type: application/vnd.ms-excelcp");
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

    for ($i=1; $i < count($rows) ; $i++) { 
        $envio = Envio::find($rows[$i][0]);
        
        $envio->cp = $rows[$i][1] ;
        $envio->costo = $rows[$i][2] ;
        
        $envio->save();
    }
    
    return redirect()->route('excelcp.index')->with('info','BD actualizada con éxito');
}

}
