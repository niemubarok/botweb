<?php

namespace App\Http\Controllers;
error_reporting (E_ALL ^ E_NOTICE);
use App\Models\JadwalDokter;
use App\Models\Poli;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class jadwalDokterController extends Controller
{
    
    public function __invoke(Request $request)
    {
        $pesan= Request('senderMessage');
        $pesan = explode('#', $pesan);
        $poliDariPesan = $pesan[1];
        $hariPraktek = $pesan[2];
        $date = date('Y-m-d');

    
        function dayDate($day) {
            return new DateTime('next ' . $day);
        }
        
            
        
        switch(strtolower($hariPraktek)){
            case "senin":
            case "senen":
            case "snen":
                $tglPraktek = dayDate('Mon')->format('Y-m-d');
            break;
            case "selasa":
            case "salasa":
            case "slasa":
                $tglPraktek = dayDate('Tue')->format('Y-m-d');
            break;
            case "rabu":
            case "rabo":
            case "rebo":
            case "rebu":
                $tglPraktek= dayDate('Wed')->format('Y-m-d');
            break;
            case "kamis":
            case "kemis":
            case "kmis":
                $tglPraktek= dayDate('Thu')->format('Y-m-d');
            break;
            case "jumat":
            case "jum'at":
            case "jm'at":
            case "jmat":
            case "jmt":
                $tglPraktek= dayDate('Fri')->format('Y-m-d');
            break;
            case "sabtu":
            case "saptu":
            case "sbt":
            case "sptu":
            case "sbtu":
                $tglPraktek= dayDate('Sat')->format('Y-m-d');
            break;
            case "minggu":
            case "mnggu":
            case "mngg":
            case "ahad":
            case "akhad":
                $tglPraktek= dayDate('Sun')->format('Y-m-d');
            break;
            case "hari ini":
            case "hr ini":
            case "hrini":
            case "hariini":
                $tglPraktek =$date;
            break;
            case "besok":
                $tglPraktek = date('Y-m-d', strtotime($date. '+ 1 day'));
            break;
            case "lusa":
                $tglPraktek = date('Y-m-d', strtotime($date. '+ 2 day'));
            break;
        default:
        return ['data'=>
        [['message' =>"mohon cek kembali nama hari yang anda masukan"]]];
        }
        
        $tglKeHari = strtotime($tglPraktek);
        $tentukanHari = date('D', $tglKeHari);
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

       //mencari keywords syaraf yang diketikan pasien
       $keywordsSyaraf = ['neurologi', 'SARAF', 'neurolog', 'sp.n','neu','spn','syaraf','syrf','sraf'];
       $matchSyaraf = array_map('stristr', array_fill(0, count($keywordsSyaraf), $poliDariPesan), $keywordsSyaraf);
       $matchSyaraf = implode($matchSyaraf);
 
       //mencari keywords obgyn yang diketikan pasien
       $keywordsObgyn = ['kndungan', 'kandungan', 'obgin', 'spog', 'Sp.og','kndngn', 'obg'];
       $matchObgyn = array_map('stristr', array_fill(0, count($keywordsObgyn), $poliDariPesan), $keywordsObgyn);
       $matchObgyn = implode($matchObgyn);
 
       //mencari keywords internis yang diketikan pasien
       $keywordsPoliDalam = ['internis', 'int', 'intrenis', 'intrns', 'penyakit dalam', 'pnykt dlm', 'pny dlm', 'p.dlm','sppd', 'poli dalam', 'polidalam'];
       $matchPoliDalam = array_map('stristr', array_fill(0, count($keywordsPoliDalam), $poliDariPesan), $keywordsPoliDalam);
       $matchPoliDalam = implode($matchPoliDalam);
 
       //replace keyword dengan nama poli yang sesuai dengan database
           if ($matchObgyn){
               $poliDariPesan = substr_replace($matchObgyn, "obgyn", 0);
           }else if($matchSyaraf){
               $poliDariPesan = substr_replace($matchSyaraf, "syaraf", 0);
           }else if($matchPoliDalam){
             $poliDariPesan = substr_replace($matchPoliDalam, "Poli Dalam", 0);
         }else{
               $poliDariPesan;
           }
 
            
        $ambilPoli = Poli::where('nm_poli', 'LIKE','%'.$poliDariPesan.'%')->first();
        $kodePoli = $ambilPoli->kd_poli;
        $nmPoli = $ambilPoli->nm_poli;
        $poli = Poli::pluck('nm_poli');
        $listPoli = collect($poli);
        $jadwalDokter = DB::table('jadwal')->where('kd_poli', $kodePoli)->where('hari_kerja', $hari)->join('dokter', function($join){
            $join->on('dokter.kd_dokter', '=', 'jadwal.kd_dokter');
          })->get();
          
            
       
        if($poliDariPesan == str_contains("poli umum", $poliDariPesan)){
        
            $res= ['data'=>
            [['message' =>"Mohon maaf hanya tersedia poli spesialis. \nBerikut nama poli yang tersedia: \n-$listPoli[4]\n-$listPoli[5]\n-$listPoli[6]\n-$listPoli[8]\n-$listPoli[9]\n-$listPoli[10]\n-$listPoli[11]\n-$listPoli[12]\n-$listPoli[13]\n-$listPoli[15]\n-$listPoli[16]"
            ]]];
            return $res;
        }else if(!isset($ambilPoli)){
        
        $res= ['data'=>
        [['message' =>"Mohon maaf poli yang anda tuju tidak ada, berikut nama poli yang tersedia: \n-$listPoli[4]\n-$listPoli[5]\n-$listPoli[6]\n-$listPoli[8]\n-$listPoli[9]\n-$listPoli[10]\n-$listPoli[11]\n-$listPoli[12]\n-$listPoli[13]\n-$listPoli[15]\n-$listPoli[16]"
        ]]];
        return $res; 
       }else{
        
          $jamMulai= $jadwalDokter->pluck('jam_mulai');
          $jamMulaiSatu = date('H:i',$jamMulai[0]);
          $jamMulaiDua = date('H:i', $jamMulai[1]);
          $jamMulaiTiga = date('H:i', $jamMulai[2]);
          $jamMulaiEmpat = date('H:i', $jamMulai[3]);
          $jamMulaiLima = date('H:i', $jamMulai[4]);
          $jamMulaiEnam = date('H:i', $jamMulai[5]);
          $jamMulaiTujuh = date('H:i', $jamMulai[6]);
  
          $jamSelesai= $jadwalDokter->pluck('jam_selesai');
          $jamSelesaiSatu = date('H:i', $jamSelesai[0]); 
          $jamSelesaiDua = date('H:i', $jamSelesai[1]); 
          $jamSelesaiTiga = date('H:i', $jamSelesai[2]); 
          $jamSelesaiEmpat = date('H:i', $jamSelesai[3]); 
          $jamSelesaiLima = date('H:i', $jamSelesai[4]); 
          $jamSelesaiEnam = date('H:i', $jamSelesai[5]); 
          $jamSelesaiTujuh = date('H:i', $jamSelesai[6]); 
  
          $jadwalDokter= $jadwalDokter->pluck('nm_dokter');
            if(isset($jadwalDokter[0])){
            $dokterSatu = "\n-".$jadwalDokter[0]." (".$jamMulaiSatu."-".$jamSelesaiSatu.")";
            }
            if(isset($jadwalDokter[1])){
            $dokterDua = "\n-".$jadwalDokter[1]." (".$jamMulaiDua ."-". $jamSelesaiDua.")";
            }
            if(isset($jadwalDokter[2])){
            $dokterTiga = "\n-".$jadwalDokter[2]. " (".$jamMulaiTiga ."-". $jamSelesaiTiga.")";
            }
            if(isset($jadwalDokter[3])){
            $dokterEmpat = "\n-".$jadwalDokter[3]. " (".$jamMulaiEmpat ."-". $jamSelesaiEmpat.")";
            }
            if(isset($jadwalDokter[4])){
            $dokterLima = "\n-".$jadwalDokter[4]. " (".$jamMulaiLima ."-". $jamSelesaiLima.")" ;
            }
            if(isset($jadwalDokter[5])){
            $dokterEnam = "\n-".$jadwalDokter[5]." (".$jamMulaiEnam ."-". $jamSelesaiEnam.")";
            }
            if(isset($jadwalDokter[6])){
            $dokterTujuh = "\n-".$jadwalDokter[6]. " (".$jamMulaiTujuh ."-". $jamSelesaiTujuh.")";
            }
  
            if(isset($jadwalDokter[0]) || isset($jadwalDokter[1]) || isset($jadwalDokter[2]) || isset($jadwalDokter[3]) || isset($jadwalDokter[4]) || isset($jadwalDokter[5]) || isset($jadwalDokter[6])){
  
              $res= ['data'=>
              [['message' =>"Berikut jadwal $nmPoli $hari:$dokterSatu$dokterDua $dokterTiga$dokterEmpat $dokterLima$dokterEnam $dokterTujuh\n \nUntuk mendaftar silahkan ketik: \n*\"REG#NIK/RM#POLI#DOKTER#HARI INI/ BESOK/ LUSA\"*"]]];
              return response()->json($res);
            }else{
                return ['data'=>
              [['message' =>"Mohon maaf hari $hariPraktek tidak ada jadwal $nmPoli. \nSilahkan cek dihari lainnya. \nTerimakasih"]]];
            }
       }







    }
}
