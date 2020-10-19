<?php

namespace App\Traits;

use DateTime;
use App\Traits\responseTrait;

trait timeTrait
{
    use responseTrait;


    function dayDate($day) {
        return new DateTime($day);
    }

    public function hariKeTgl()
    {
            //tanggal pendaftaran
        $date = date('Y-m-d');
        $hari = $this->getDayFromMessage();
        switch(strtolower($hari)){
            case "senin":
            case "senen":
            case "snen":
                $hari = $this->dayDate('Mon')->format('Y-m-d');
                
            break;
            case "selasa":
            case "selas":
            case "salasa":
            case "slsa":
            case "slasa":
                $hari = $this->dayDate('Tue')->format('Y-m-d');
            break;
            case "rabu":
            case "rabo":
            case "rebo":
            case "rebu":
                $hari= $this->dayDate('Wed')->format('Y-m-d');
            break;
            case "kamis":
            case "kemis":
            case "kmis":
                $hari= $this->dayDate('Thu')->format('Y-m-d');
            break;
            case "jumat":
            case "jum'at":
            case "jm'at":
            case "jmat":
            case "jmt":
                $hari= $this->dayDate('Fri')->format('Y-m-d');
            break;
            case "sabtu":
            case "saptu":
            case "sbt":
            case "sptu":
            case "sbtu":
                $hari= $this->dayDate('Sat')->format('Y-m-d');
            break;
            case "minggu":
            case "mnggu":
            case "mngg":
            case "ahad":
            case "akhad":
                $hari= $this->dayDate('Sun')->format('Y-m-d');
            break;
            case "hari ini":
            case "hr ini":
            case "hrini":
            case "hariini":
                $hari =$date;
            break;
            case "besok":
                $hari = date('Y-m-d', strtotime($date. '+ 1 day'));
            break;
            case "lusa":
                $hari = date('Y-m-d', strtotime($date. '+ 2 day'));
            break;
            default:
                $hari = $date;
        }
        return $hari;

    }


    //mengubah hari dalam bahasa inggris ke hari dalam bahasa indonesia/sesuai di database
    public function dayToHari() 
    {
        $strTotime      = strtotime($this->hariKeTgl());
        $tentukanHari   = date('D', $strTotime);
        $day = array(
        'Sun' => 'AKHAD',
        'Mon' => 'SENIN',
        'Tue' => 'SELASA',
        'Wed' => 'RABU',
        'Thu' => 'KAMIS',
        'Fri' => 'JUMAT',
        'Sat' => 'SABTU'
        );

        $hari=$day[$tentukanHari];
        return $hari;
    }

    // public function hariKeTgl()
    // {
    //     $hari = $this->getHari();
    //     $strTotime =strtotime($hari);
    //     $tgl =date('Y-m-d', $strTotime);
    //     return $tgl;
    // }

    public function tglLahir()
    {
        $getTglLahir = $this->getBirthDate();
        switch(true){
          case str_contains($getTglLahir,"januari"):
            $getTglLahir = str_replace("januari", "jan", $getTglLahir);
          break;
          case str_contains($getTglLahir,"februari"):
            $getTglLahir = str_replace("februari", "feb", $getTglLahir);
          break;
          case str_contains($getTglLahir,"febuari"):
            $getTglLahir = str_replace("febuari", "feb", $getTglLahir);
          break;
          case str_contains($getTglLahir,"febuary"):
            $getTglLahir = str_replace("febuary", "feb", $getTglLahir);
          break;
          case str_contains($getTglLahir,"maret"):
            $getTglLahir = str_replace("maret", "march", $getTglLahir);
          break;
          case str_contains($getTglLahir,"mart"):
            $getTglLahir = str_replace("mart", "march", $getTglLahir);
          break;
          case str_contains($getTglLahir,"mei"):
            $getTglLahir = str_replace("mei", "may", $getTglLahir);
          break;
          case str_contains($getTglLahir,"oktober"):
            $getTglLahir = str_replace("oktober", "october", $getTglLahir);
          break;
          case str_contains($getTglLahir,"desember"):
            $getTglLahir = str_replace("desember", "december", $getTglLahir);
          break;

        }

        $tglLahirToTime = strtotime($getTglLahir);
        $tglLahir = date('Y-m-d',$tglLahirToTime);
        return $tglLahir;
    }
}















