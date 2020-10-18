<?php

namespace App\Http\Controllers;

use App\Traits\messageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\handleController;
use App\Traits\pasienTrait;
use App\Traits\poliTrait;
use Illuminate\Support\Arr;

class tesController extends Controller
{
    // use messageTrait;
    use pasienTrait;
    use poliTrait;

    public function __invoke(Request $request)
    {

        // $senderMessage = Str::of($this->senderMessage())->containsAll(['nama', 'ktp', 'lahir(tgl-bln-thn)', 'poli tujuan:']);
        return $this->poli();
        // echo json_encode($this->messageValidation());

        // if($senderMessage){


        // }else{
            // print session('senderMessage');
        // }





        //    $patientName = $this->getPatientName();
        //     // echo $patientName;
        //    $birtDate = $this->getBirthDate();
        // //    echo json_encode($birtDate);
        //    $poly = $this->getPoly();
        //    // echo $poly;
        //    $doctor = $this->getDoctor();
        //    // echo $doctor;
        //    $day = $this->getDay();
           // echo $day;


    }
}
