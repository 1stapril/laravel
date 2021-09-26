<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberCtrl extends Controller
{
    public function import(Request $request){
        $data = [];
        $data['records'] = Subscriber::latest()->paginate(20);
        return view('import')->with($data);
    }

    public function import_post(Request $request){
        $request->validate([
            'csv' => 'required',
        ]);
  
        $input = $request->all();
  
        if ($csv = $request->file('csv')) {
            $csvDestinationPath = 'uploads/';
            $postCsv = date('YmdHis') . "." . $csv->getClientOriginalExtension();
            $csv->move($csvDestinationPath, $postCsv);
            $csvPath = $csvDestinationPath.$postCsv;

            $users = $this->csvToArray($csvPath);

            foreach($users as $user){
                
                $user = ['name' => $user['Name'], 'email' => $user['Email']];
                
                Subscriber::create($user);
            }

            return redirect('import');
            
        }
    }

    public function export(){

        $list[] = ['Name','Email'];
        $records = Subscriber::select('name','email')->latest()->get();
        foreach($records as $record) {
            $list[] = [$record->name,$record->email];
        }
        $filename = 'file.csv';
        $fp = fopen($filename, 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        
        @header("Content-type: application/zip");
        @header("Content-Disposition: attachment; filename=$filename");
        echo file_get_contents('file.csv');

        if(file_exists($filename)) {
            unlink($filename);
        } 

        //return redirect('import');

        
        
    }    

    public function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
}
