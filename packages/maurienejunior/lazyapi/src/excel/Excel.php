<?php

namespace maurienejunior\lazyapi\excel;

class Excel{
    public static function array_to_csv_download($data, $fields, $filename = "export.csv", $delimiter=";") {

        $f = fopen('php://memory', 'w'); 

        $keys_arr = [];

        foreach($fields as $key => $field){
            array_push($keys_arr, $key);
        }

        fputcsv($f, $keys_arr, $delimiter); 

        foreach($data as $d){

            $arr_itens = [];

            foreach ($fields as $key => $field){
                array_push($arr_itens, $d->$field);
            }

            fputcsv($f, $arr_itens, $delimiter); 
        }


        fseek($f, 0);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        fpassthru($f);
    }
}