<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\RegPeriksa;
use App\Traits\dokterTrait;
use Illuminate\Http\Request;
use App\Traits\stringsTrait;
use Twilio\TwiML\MessagingResponse;
use App\Traits\responseTrait;
use Illuminate\Support\Arr;
use App\Traits\agTrait;

trait messageTrait
{
    // use dokterTrait;
    use responseTrait;
    use stringsTrait;
    use agTrait;

    public  $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function messageValidation()
    {
        // $response = new MessagingResponse();

        $messageEmptyNamaPasien     = "- *nama pasien sesuai KTP*";
        $messageEmptyBirthDate      = "- *tanggal lahir pasien dengan format (tgl-bln-thn)*";
        $messageEmptyPoli           = "- *poli tujuan*";
        $messageEmptyDokter         = "- *dokter tujuan*";
        $messageEmptyDay            = "- *hari berobat*";

        $emptyNamaPasien    = Str::of($this->getNamaPasien())->trim()->isEmpty();
        $emptyBirthDate     = Str::of($this->getBirthDate())->trim()->isEmpty();
        $emptyPoli          = Str::of($this->getPoli())->trim()->isEmpty();
        $emptyDokter        = Str::of($this->getDokter())->trim()->isEmpty();
        $emptyDay           = Str::of($this->getDayFromMessage())->trim()->isEmpty();

        $messageEmptyNamaPasien = $emptyNamaPasien == true?$messageEmptyNamaPasien.".\n ":'';
        $messageEmptyBirthDate = $emptyBirthDate == true?$messageEmptyBirthDate.".\n ":'';
        $messageEmptyPoli = $emptyPoli == true?$messageEmptyPoli.".\n ":'';
        $messageEmptyDokter = $emptyDokter == true?$messageEmptyDokter.".\n ":'';
        $messageEmptyDay = $emptyDay == true ? $messageEmptyDay.".\n":'';

         $message= $messageEmptyNamaPasien || $messageEmptyBirthDate || $messageEmptyPoli || $messageEmptyDokter || $messageEmptyDay == true ? "Mohon ulangi dan lengkapi data berikut:\n".$messageEmptyNamaPasien.$messageEmptyBirthDate.$messageEmptyPoli.$messageEmptyDokter.$messageEmptyDay: '';
         $message= $this->reply($message);

         $senderMessage = Str::of($this->senderMessage())->containsAll(['nama', 'ktp', 'lahir','dokter','poli']);
         if($senderMessage == true){

            return $message;
        }
    }



    public function incomingMessage()
    {
        $incomingMessage = request();
        return $incomingMessage;
    }

    public function storeSession($key, $value)
    {
        return $this->request->session()->put([$key => $value]);
    }


    function sessionMessage()
    {
        $senderMessage = Str::of($this->senderMessage())->containsAll(['nama', 'ktp', 'lahir(tgl-bln-thn)', 'poli tujuan:']);

        if($senderMessage){
            $sessionMessage = $this->storeSession('senderMessage', strtolower(request('senderMessage')));
            return $sessionMessage;
        }
    }


    public function senderMessage()
    {
    
       return strtolower($this->request['data']['message']['pesan']);
    }


    public function getContact()
    {
        return request('From');
    }

    public function messageSid()
    {
        return request('MessageSid');
    }

    public function isContactExist()
    {
        $isContactExist = RegPeriksa::select('kontak_wa')->where('kontak_wa', $this->getContact())->first();
        return $isContactExist->kontak_wa;
    }

    public function getNoKtp() //ambil nama pasien
    {
        $noKtp = Str::between($this->senderMessage(),"*no. ktp/rekam medis*:", "\n*nama sesuai ktp*:" );
        return trim($noKtp, ' ');
    }

    public function getRm()
    {
        $noRm = Str::between($this->senderMessage(),"*no. ktp/rekam medis*:", "\n*nama sesuai ktp*:" );
        return trim($noRm, ' ');

    }

    public function getNamaPasien() //ambil nama pasien
    {

        $namaPasien = Str::between($this->senderMessage(),"*nama sesuai ktp*:","\n*lahir(tgl-bln-thn)*:" );
        return trim($namaPasien, ' ');
    }

    public function getBirthDate()
    {
        $tglLahir  = Str::between($this->senderMessage(), "*lahir(tgl-bln-thn)*:", "\n*poli tujuan*:");
        $tglLahir  = trim($tglLahir, ' ');
        return $tglLahir;
    }

    public function getPoli()
    {
        $poliTujuan = trim(Str::between($this->senderMessage(), "*poli tujuan*:", "\n*dokter tujuan*:"), ' ');
        // if(Str::of($poliTujuan)->trim()->isEmpty()){
        //     return "tidak ada";
        // }else{

            return $poliTujuan;
        // }

    }

    public function getDokter()
    {
        $dokter = Str::between($this->senderMessage(),"*dokter tujuan*:", "\n*hari*");
        $containsDr = Str::of($dokter)->contains(["dr.","dr","dokter"]);

        if($containsDr == true){
            $dokter = Str::after($dokter, "dokter");
            $dokter = Str::after($dokter, "dr.");
            $dokter = Str::after($dokter, "dr");
            return trim($dokter, ' ');

        }else{

            return trim($dokter, ' ');
        }
    }

    public function getDayFromMessage()
    {
        $day = trim(Str::after($this->senderMessage(), "*hari*:"),"\"");
        return ltrim(rtrim($day, ' '),' ');
    }

}
