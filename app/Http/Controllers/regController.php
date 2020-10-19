<?php

namespace App\Http\Controllers;
error_reporting (E_ALL ^ E_NOTICE);


use App\Models\Poli;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\RegPeriksa;
use App\Models\JadwalDokter;
use App\Traits\messageTrait;
use App\Traits\stringsTrait;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class regController extends Controller
{

    use messageTrait;
    use stringsTrait;

    function dayDate($day) {
      return new DateTime('next ' . $day);
    }

    public function __invoke(Request $request)
    {
        //pesan masuk
      $pesanMasuk = strtolower(Request('senderMessage'));
      //pecah pesan dari pasien "[reg] [nik] [poli] [dokter]
      $pesanMasuk= $this->splitCrash($pesanMasuk);
    //   dd($pesanMasuk);

      //ambil nama pasien
      $nmPasien = $pesanMasuk[1];
    //   $nmPasien = str_replace("nama lengkap: ","",$nmPasien);
    //   $nmPasien = str_replace("nama lengkap:","",$nmPasien);
      $nmPasien = rtrim($nmPasien," ");
      $nmPasien = ltrim($nmPasien," ");
    //   print($nmPasien);

      //ambil rm
      $noRekamMedis = $pesanMasuk[2];

      //ambil no KTP
      $noKtp = $pesanMasuk[2];

    //   dd($nmPasien);
      //ambil tgl lahir
      $getTglLahir = ltrim($pesanMasuk[2],"Lahir(tgl-bln-thn): ");
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

    //   dd($tglLahir);
        //ambil poli tujuan yg diketik pasien
      $poliTujuan = str_replace(' ', '',$pesanMasuk[3]);
      $poliTujuan = str_replace("poli:","",$poliTujuan);
        if(str_contains($poliTujuan, "poli ")){
          $poliTujuan = str_replace( "poli ", "", $poliTujuan);
        }else{
          $poliTujuan = str_replace( "poli", "", $poliTujuan);
        }
        // echo $poliTujuan;
      //ambil nama dokter
      $dokterTujuan = str_replace(' ', '',$pesanMasuk[4]);
    //   $dokterTujuan = str_replace("dokter:", "", $dokterTujuan);
    $dokterTujuan = Str::after($dokterTujuan, 'dr.');
    $dokterTujuan = Str::after($dokterTujuan, 'dokter');
      $dokterTujuan = ltrim($dokterTujuan, ' ');

      //ambil tanggal daftar
      $tglRegistrasi = strtolower($pesanMasuk[5]);
      $tglRegistrasi =  str_replace("hari: ","",$tglRegistrasi);

       //tanggal pendaftaran
       $date = date('Y-m-d');

       $_hariIni = date('Y-m-d');
      //  $hariIni = substr_replace($_hariIni, "hari ini ($_hariIni)", 0);
       $_besok = date('Y-m-d', strtotime($_hariIni. '+ 1 day'));
      //  $besok = substr_replace($_besok, "besok ($_besok)", 0);
       $_lusa = date('Y-m-d', strtotime($_besok. '+1 day'));
      //  $lusa = substr_replace($_lusa, "lusa ($_lusa)", 0);

       switch(strtolower($tglRegistrasi)){
        case "senin":
        case "senen":
        case "snen":
            $tglRegistrasi = $this->dayDate('Mon')->format('Y-m-d');
        break;
        case "selasa":
        case "selas":
        case "salasa":
        case "slsa":
        case "slasa":
            $tglRegistrasi = $this->dayDate('Tue')->format('Y-m-d');
        break;
        case "rabu":
        case "rabo":
        case "rebo":
        case "rebu":
            $tglRegistrasi= $this->dayDate('Wed')->format('Y-m-d');
        break;
        case "kamis":
        case "kemis":
        case "kmis":
            $tglRegistrasi= $this->dayDate('Thu')->format('Y-m-d');
        break;
        case "jumat":
        case "jum'at":
        case "jm'at":
        case "jmat":
        case "jmt":
            $tglRegistrasi= $this->dayDate('Fri')->format('Y-m-d');
        break;
        case "sabtu":
        case "saptu":
        case "sbt":
        case "sptu":
        case "sbtu":
            $tglRegistrasi= $this->dayDate('Sat')->format('Y-m-d');
        break;
        case "minggu":
        case "mnggu":
        case "mngg":
        case "ahad":
        case "akhad":
            $tglRegistrasi= dayDate('Sun')->format('Y-m-d');
        break;
        case "hari ini":
        case "hr ini":
        case "hrini":
        case "hariini":
            $tglRegistrasi =$date;
        break;
        case "besok":
            $tglRegistrasi = date('Y-m-d', strtotime($date. '+ 1 day'));
        break;
        case "lusa":
            $tglRegistrasi = date('Y-m-d', strtotime($date. '+ 2 day'));
        break;
    default:
    return ['data'=>
    [['message' =>"mohon cek kembali nama hari yang anda masukan"]]];
    }
       $tglKeHari = strtotime($tglRegistrasi);
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

        if($tglRegistrasi === $_hariIni){
          $tglRegistrasiStr = substr_replace($tglRegistrasi, "hari ini ($_hariIni)", 0);
        }else if($tglRegistrasi === $_besok){
          $tglRegistrasiStr = substr_replace($tglRegistrasi, "besok ($_besok)", 0);
        }else if($tglRegistrasi === $_lusa){
          $tglRegistrasiStr = substr_replace($tglRegistrasi, "lusa ($_lusa)", 0);
        }

      //mencari keywords syaraf yang diketikan pasien
      $keywordsSyaraf = ['neurologi', 'SARAF', 'neurolog', 'sp.n','neu','spn'];
      $matchSyaraf = array_map('stristr', array_fill(0, count($keywordsSyaraf), $poliTujuan), $keywordsSyaraf);
      $matchSyaraf = implode($matchSyaraf);

      //mencari keywords obgyn yang diketikan pasien
      $keywordsObgyn = ['kndungan', 'kandungan', 'obgin', 'spog', 'Sp.og','kndngn', 'obg'];
      $matchObgyn = array_map('stristr', array_fill(0, count($keywordsObgyn), $poliTujuan), $keywordsObgyn);
      $matchObgyn = implode($matchObgyn);

      //mencari keywords internis yang diketikan pasien
      $keywordsPoliDalam = ['internis', 'int', 'intrenis', 'intrns', 'penyakit dalam', 'pnykt dlm', 'pny dlm', 'p.dlm','sppd', 'poli dalam', 'polidalam', 'polidlm', 'poli dlm' ];
      $matchPoliDalam = array_map('stristr', array_fill(0, count($keywordsPoliDalam), $poliTujuan), $keywordsPoliDalam);
      $matchPoliDalam = implode($matchPoliDalam);

      //replace keyword dengan nama poli yang sesuai dengan database
        if ($matchObgyn){
            $poliTujuan = substr_replace($matchObgyn, "obgyn", 0);
        }else if($matchSyaraf){
            $poliTujuan = substr_replace($matchSyaraf, "syaraf", 0);
        }else if($matchPoliDalam){
          $poliTujuan = substr_replace($matchPoliDalam, "Poli Dalam", 0);
        }else{
          $poliTujuan;
        }



      //mencari keywords kata "dokter"
      $keywordsDokter = ['dokter', 'doktor', 'duktur', 'dkter','doktr', 'dktr'];
      $matchDokter = array_filter(array_map('stristr', array_fill(0, count($keywordsDokter), $dokterTujuan), $keywordsDokter));
      $keywordsDokter = implode($matchDokter);

          if($keywordsDokter){
          $dokterTujuan = ltrim($keywordsDokter, "dokter");
          }else{
            $dokterTujuan = ltrim($dokterTujuan, "dr. ");
          }
          // echo $dokterTujuan;
      //mencari data pasien dari database sesuai nik atau rm
    //   $pasien = Pasien::where('tgl_lahir', $tglLahir)->Where('nm_pasien', 'like',"%$nmPasien%")->first();

      $pasien = Pasien::where('no_rkm_medis', $noRekamMedis)->orWhere('no_ktp', $noKtp)->orWhere(function($query)use($tglLahir, $nmPasien){
          $query->where('tgl_lahir', $tglLahir)->Where('nm_pasien', 'like',"%$nmPasien%");
        })->first();


      $noRm = $pasien->no_rkm_medis;

          //mencari poli dari database sesuai poli yang diketik pasien
      $ambilPoli = Poli::where('nm_poli', 'LIKE',"%$poliTujuan%")->first();
      $namaPoli= $ambilPoli->nm_poli;

      $kodePoli = $ambilPoli->kd_poli;
      $poliTujuan = JadwalDokter::where('kd_poli',$kodePoli)->first();
      $poliTujuan = $poliTujuan->poli['nm_poli'];

      $dokterTujuan = Dokter::where('nm_dokter', 'LIKE', "%$dokterTujuan%")->first();
      $kodeDokter = $dokterTujuan->kd_dokter;

      //mencari jadwal dokter jika pasien mengetikan nama dokter tidak sesuai spesialisnya.
      $jadwalDokterSesuaiPoli= JadwalDokter::where('kd_poli', $kodePoli)->where('hari_kerja', $hari)->join('dokter', 'dokter.kd_dokter','=', 'jadwal.kd_dokter')->get();

      $nmDokterUntukJadwal= $jadwalDokterSesuaiPoli->pluck('nm_dokter');
      $nmPoliUntukJadwal = $jadwalDokterSesuaiPoli->pluck('poli.nm_poli');

      $jamMulai = $jadwalDokterSesuaiPoli->pluck('jam_mulai');
      $jamSelesai= $jadwalDokterSesuaiPoli->pluck('jam_selesai');

      //dokter tujuan jika ditemukan di jadwal dokter
      $dokterSesuaiJadwal= JadwalDokter::where('kd_poli', $kodePoli)->where('kd_dokter', $kodeDokter)->where('hari_kerja', $hari)->first();

      $jamMulaiUntukDaftar=$dokterSesuaiJadwal->jam_mulai;
      $jamSelesaiUntukDaftar=$dokterSesuaiJadwal->jam_selesai;

      $dokterTujuan = Dokter::where('kd_dokter', $dokterSesuaiJadwal->kd_dokter)->first();
      $nmDokterUntukDaftar=  $dokterTujuan->dokter['nm_dokter'];

      //Validasi data yang dikirim dari pasien
      if(!isset($pasien)){

          $res =  ['data'=>
              [['message' =>"Mohon maaf kami tidak dapat menemukan data atas nama : *$nmPasien* dengan tanggal lahir *$tglLahir*. Mohon cek kembali. \nHubungi Customer care kami di nomor berikut: (021)-7270681"]]];
              return response()->json($res);

      }else if(!isset($kodePoli)){
          $poli = Poli::pluck('nm_poli');
          $listPoli = collect($poli);
          $res= ['data'=>
          [['message' =>"Mohon cek kembali nama poli yang anda tuju, berikut nama poli yang tersedia: \n-$listPoli[4]\n-$listPoli[5]\n-$listPoli[6]\n-$listPoli[8]\n-$listPoli[9]\n-$listPoli[10]\n-$listPoli[11]\n-$listPoli[12]\n-$listPoli[13]\n-$listPoli[15]\n-$listPoli[16]"
          ]]];
          return $res;
      }else if(blank($jadwalDokterSesuaiPoli)){
        $res= ['data'=>
        [['message' =>"Mohon maaf $tglRegistrasiStr tidak ada jadwal $namaPoli"],
        ['message'=>"Silahkan ketik *Jadwal#nama poli#hari* untuk melihat jadwal"]]];
        return response()->json($res);

      }else if(!isset($dokterTujuan)){
        $jamMulaiSatu = $jamMulai[0];
        $jamMulaiDua = $jamMulai[1];
        $jamMulaiTiga = $jamMulai[2];
        $jamMulaiEmpat = $jamMulai[3];
        $jamMulaiLima = $jamMulai[4];
        $jamMulaiEnam = $jamMulai[5];
        $jamMulaiTujuh = $jamMulai[6];


        $jamSelesaiSatu = $jamSelesai[0];
        $jamSelesaiDua = $jamSelesai[1];
        $jamSelesaiTiga = $jamSelesai[2];
        $jamSelesaiEmpat = $jamSelesai[3];
        $jamSelesaiLima = $jamSelesai[4];
        $jamSelesaiEnam = $jamSelesai[5];
        $jamSelesaiTujuh = $jamSelesai[6];

          if(isset($nmDokterUntukJadwal[0])){
          $dokterSatu = "\n-".$nmDokterUntukJadwal[0]."(".$jamMulaiSatu."-".$jamSelesaiSatu.")";
          }
          if(isset($nmDokterUntukJadwal[1])){
          $dokterDua = "\n-".$nmDokterUntukJadwal[1]."(".$jamMulaiDua ."-". $jamSelesaiDua.")";
          }
          if(isset($nmDokterUntukJadwal[2])){
          $dokterTiga = "\n-".$nmDokterUntukJadwal[2]. "(".$jamMulaiTiga ."-". $jamSelesaiTiga.")";
          }
          if(isset($nmDokterUntukJadwal[3])){
          $dokterEmpat = "\n-".$nmDokterUntukJadwal[3]. "(".$jamMulaiEmpat ."-". $jamSelesaiEmpat.")";
          }
          if(isset($nmDokterUntukJadwal[4])){
          $dokterLima = "\n-".$nmDokterUntukJadwal[4]. "(".$jamMulaiLima ."-". $jamSelesaiLima.")" ;
          }
          if(isset($nmDokterUntukJadwal[5])){
          $dokterEnam = "\n-".$nmDokterUntukJadwal[5]."(".$jamMulaiEnam ."-". $jamSelesaiEnam.")";
          }
          if(isset($nmDokterUntukJadwal[6])){
          $dokterTujuh = "\n-".$nmDokterUntukJadwal[6]. "(".$jamMulaiTujuh ."-". $jamSelesaiTujuh.")";
          }

          if(isset($nmDokterUntukJadwal[0]) || isset($nmDokterUntukJadwal[1]) || isset($nmDokterUntukJadwal[2]) || isset($nmDokterUntukJadwal[3]) || isset($nmDokterUntukJadwal[4]) || isset($nmDokterUntukJadwal[5]) || isset($nmDokterUntukJadwal[6])){


            $res= ['data'=>
            [['message' =>"Upps.. kami tidak dapat menemukan dokter yang anda tuju. \nBerikut jadwal $poliTujuan $tglRegistrasiStr:$dokterSatu$dokterDua $dokterTiga$dokterEmpat $dokterLima$dokterEnam $dokterTujuh\nGunakan nama depan atau nama belakang dokter diatas."]]];
            return response()->json($res);
          }
      }else{
            //cek apakah sudah terdaftar
        $regPeriksa = RegPeriksa::select('no_rawat','kd_dokter', 'no_reg', 'kd_poli', 'no_rkm_medis')->where('no_rkm_medis', $noRm)->where('tgl_registrasi', $tglRegistrasi)->where('kd_dokter', $kodeDokter)->where('kd_poli', $kodePoli)->first();


          //jika sudah terdaftar
        if(!empty($regPeriksa)){
          if($tglRegistrasi === $_hariIni){
            $tglRegistrasiStr = substr_replace($tglRegistrasi, "hari ini ($_hariIni)", 0);
          }else if($tglRegistrasi === $_besok){
            $tglRegistrasiStr = substr_replace($tglRegistrasi, "besok ($_besok)", 0);
          }else if($tglRegistrasi === $_lusa){
            $tglRegistrasiStr = substr_replace($tglRegistrasi, "lusa ($_lusa)", 0);
          }


          return ["data"=>
          [[
            "message" => "Alhamdulillah! \n*$hari($tglRegistrasi)* anda sudah terdaftar ke *{$regPeriksa->poli['nm_poli']}* dengan *{$regPeriksa->dokter['nm_dokter']}*. Praktek pkl. $jamMulaiUntukDaftar - $jamSelesaiUntukDaftar dapat antrian no. *{$regPeriksa['no_reg']}* \nSegera informasikan kepada kami jika anda berhalangan hadir. \nTerimakasih."
          ]]
        ];
        }else{ //jika belum terdaftar
          //respon ke pasien dengan detail pendaftaran.
          //  ambil no rawat terakhir
          $no_rawat_akhir = RegPeriksa::where('tgl_registrasi', $tglRegistrasi)->max('no_rawat');
          $no_urut_rawat = substr($no_rawat_akhir, 11, 6);
          $tgl_reg_no_rawat = date('Y/m/d', strtotime($tglRegistrasi));
          $no_rawat = $tgl_reg_no_rawat.'/'.sprintf('%06s', ($no_urut_rawat + 1));

          //mencari no reg terakhir
          $no_reg = RegPeriksa::where('kd_dokter', $kodeDokter)->where('tgl_registrasi', $tglRegistrasi)->max('no_reg');

          //  $no_urut_reg = substr($no_reg, 0, 3);
          $no_reg = sprintf('%03s', ($no_reg + 1));


          $biaya_reg = Poli::select('registrasilama')->where('kd_poli', $kodePoli)->first();
          $biaya_reg= $biaya_reg['registrasilama'];

          //menentukan umur sekarang
          $birthDate = $pasien->tgl_lahir;
          $birthDate = explode('-', $birthDate);
          $y = $birthDate[0];
          $cy = date('Y');
          $umur = $cy-$y;

          //jam registrasi
          $jam = date('H:i:s');

          $jamPraktek = JadwalDokter::select('jam_mulai', 'jam_selesai')->where('kd_dokter', $kodeDokter)->first();
          $jamMulai = $jamPraktek->jam_mulai;
          $jamSelesai = $jamPraktek->jam_selesai;

          $store = RegPeriksa::create([
              'no_reg'          => $no_reg,
              'no_rawat'        => $no_rawat,
              'tgl_registrasi'  => $tglRegistrasi,
              'jam_reg'         => $jam,
              'kd_dokter'       => $dokterTujuan['kd_dokter'],
              'no_rkm_medis'    => $noRm,
              'kd_poli'         => $kodePoli,
              'p_jawab'         => $pasien['namakeluarga'],
              'almt_pj'         => $pasien['alamat'],
              'hubunganpj'      => $pasien['keluarga'],
              'biaya_reg'      => $biaya_reg,
              'stts'            => 'Belum',
              'stts_daftar'     => 'Lama',
              'status_lanjut'   => 'Ralan',
              'kd_pj'           => $pasien['kd_pj'],
              'umurdaftar'      => $umur,
              'sttsumur'        => 'Th',
              'status_bayar'    => 'Belum Bayar'

          ]);
          $res = ['data'=>
          [['message' => "Anda sudah terdaftar \n \n--Detail Pendaftaran-- \nNama : *$pasien->nm_pasien* \nNo. RM: *$pasien->no_rkm_medis* \nPoli Tujuan : *$poliTujuan* \nDokter: *{$dokterTujuan['nm_dokter']}* \nTanggal Periksa: *$tglRegistrasi* \njam praktek : pkl. $jamMulaiUntukDaftar s/d $jamSelesaiUntukDaftar . \nNo. Antrian : *$no_reg*"],
          ['message' => "*Datanglah sesuai jam praktek dokter.* \nTunjukan pesan ini kepada petugas pendaftaran di Lobby utama. \nTerimakasih telah mempercayakan kesehatan keluarga anda di RS Ali Sibroh Malisi. \nSemoga lekas sembuh"

          ]]
        ];
        return $res;



          }
      }
    }
  }
