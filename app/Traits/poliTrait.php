<?php

namespace app\Traits;
error_reporting (E_ALL ^ E_NOTICE);

use App\Models\JadwalDokter;
use App\Traits\messageTrait;
use Illuminate\Support\Str;
use App\Models\Poli;
use App\Traits\timeTrait;
use Illuminate\Support\Arr;

trait poliTrait
{
    use messageTrait;
    use timeTrait;

    function findKeywords($keywords)
    {
        $keywords  = $keywords;
        $match     = array_map('stristr', array_fill(0, count($keywords), $this->getpoli()), $keywords);
        $match     = implode($match);
        return $match;
    }

    public function poli() //mencari poli dari database sesuai poli yang diketik pasien
    {
        $poliTujuan = $this->getPoli();

        //mencari keywords syaraf yang diketikan pasien
        $keywordsSyaraf = $this->findKeywords(['syaraf','neurologi', 'SARAF', 'neurolog', 'sp.n','neu','spn']);

        //mencari keywords obgyn yang diketikan pasien
        $keywordsObgyn  = $this->findKeywords(['kndungan', 'kandungan', 'obgin', 'obgyn', 'spog', 'Sp.og','kndngn', 'obg']);

        //mencari keywords internis yang diketikan pasien
        $keywordsPoliDalam  = $this->findKeywords(['internis', 'int', 'intrenis', 'intrns', 'penyakit dalam', 'pnykt dlm', 'pny dlm', 'p.dlm','sppd', 'poli dalam', 'polidalam', 'polidlm', 'poli dlm' ]);

        //mencari keywords spesialis anak yang diketikan pasien
        $keywordsPoliAnak  =  $this->findKeywords(['spesialis anak', 'spa','sp.a', 'anak', 'poli Anak']);

        //mencari keywords spesialis Mata yang diketikan pasien
        $keywordsPoliMata  = $this->findKeywords(['spesialis Mata', 'spm','sp.m', 'Mata', 'poli Mata', 'mata','polimata']);

        //mencari keywords spesialis Kulit yang diketikan pasien
        $keywordsPoliKulit  = $this->findKeywords(['spesialis Kulit', 'spkk','sp.kk', 'Kulit', 'poli Kulit','poliKulit']);

        // $keywordsGigi
        $keywordsGigi = $this->findKeywords(['gigi','gigi dan mulut', 'dentis','dentist', 'poli gigi']);

        $keywordsBedahUmum = $this->findKeywords(['Bedah Umum','BedahUmum','poli Bedah Umum', 'poli bedah']);

        $keywordsOrthopedi = $this->findKeywords(['Orthopaedy','Orthopedy','Orthopaedi','Orthopedi', 'ortopedi', 'ortopaedi', 'ortopeadi','Orthopeady','Orthopeady','Orthopeadi','Orthopeadi']);

        $keywordsParu = $this->findKeywords(['paru', 'poli paru', 'poliaparu','spp', 'sp.P', 'spesialis paru', 'specialis paru']);

         //replace keyword dengan nama poli yang sesuai dengan database
         if ($keywordsObgyn){
             $poliTujuan = substr_replace($keywordsObgyn, "poli obgyn", 0);
         }else if($keywordsSyaraf){
             $poliTujuan = substr_replace($keywordsSyaraf, "poli syaraf", 0);
         }else if($keywordsPoliDalam){
             $poliTujuan = substr_replace($keywordsPoliDalam, "Poli Dalam", 0);
         }else if($keywordsPoliAnak){
             $poliTujuan = substr_replace($keywordsPoliAnak, "Poli anak", 0);
         }else if($keywordsPoliMata){
            $poliTujuan = substr_replace($keywordsPoliMata, "Poli Mata", 0);
        }else if($keywordsPoliKulit){
            $poliTujuan = substr_replace($keywordsPoliKulit, "Poli Kulit", 0);
        }else if($keywordsGigi){
            $poliTujuan = substr_replace($keywordsGigi, "Poli gigi", 0);
        }else if($keywordsBedahUmum){
            $poliTujuan = substr_replace($keywordsBedahUmum, "Poli Bedah Umum", 0);
        }else if($keywordsOrthopedi){
            $poliTujuan = substr_replace($keywordsOrthopedi, "Poli Orthopedi", 0);
        }else if($keywordsParu){
            $poliTujuan = substr_replace($keywordsParu, "Poli Paru", 0);
        }else{
             $poliTujuan;
         }


        $poli = Poli::where('nm_poli','like',$poliTujuan)->first();
        return $poli;

    }

    public function kodePoli()
    {
        $kodePoli = $this->poli()['kd_poli'];
        return $kodePoli;
    }

    public function listPoli()
    {
        $listPoli   = Poli::pluck('nm_poli')->toArray();
        $listPoli   = Arr::except($listPoli, ['0','1','2','3','4','18','19','20','21']);
        $listPoli   = array_values($listPoli);
        return $listPoli;

    }

    public function poliSesuaiJadwal()
    {
        //nama poli dari table jadwal dokter
        $poliTujuan         = JadwalDokter::where('kd_poli',$this->kodePoli())->where('hari_kerja', $this->dayToHari())->first();
        $poliSesuaiJadwal   = $poliTujuan->poli['nm_poli'];
        return $poliSesuaiJadwal;
    }

    public function jadwalPoli()
    {
        $namaPoli   = JadwalDokter::where('kd_poli', $this->kodePoli())->join('dokter', 'dokter.kd_dokter', '=', 'jadwal.kd_dokter')->get();
        $namaPoli   = $namaPoli->mapToGroups(function($item, $key){
            return [$item['nm_dokter']  => [[$item['hari_kerja']." (".$item['jam_mulai']." - ".$item['jam_selesai'].")"."#"]]];
        });
        $namaPoli   = explode(']]',$namaPoli);
        $namaPoli   = implode($namaPoli);
        $namaPoli   = Str::of($namaPoli)->replaceMatches('/{|}|[[|]|]|"|"|,/', '');
        $namaPoli   = Str::of($namaPoli)->replaceMatches('/\#|:/', "\n");
        return $namaPoli;
    }

    

}
