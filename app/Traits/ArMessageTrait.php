<?php

namespace App\Traits;
use Illuminate\Support\Str;
use App\Models\RegPeriksa;
use Illuminate\Http\Request;
use App\Traits\stringsTrait;
use Twilio\TwiML\MessagingResponse;

trait ArMessageTrait
{
    use stringsTrait;

    public      $request,
                $messageEmptyPatientName = "Mohon melengkapi nama pasien sesuai KTP",
                $messageEmptyBirthDate = "mohon masukan tanggal lahir pasien dengan format (tgl-bln-thn)",
                $messageEmptyPoly= "Ingin berobat ke poli apa?",
                $messageEmptyDoctor = "Ingin berobat ke dokter siapa?",
                $messageEmptyDay = "Ingin berobat hari apa?";

    public function emptyData()
    {
        $emptyPatientName = Str::of($this->getPatientName())->trim()->isEmpty();
        $emptyBirthDate = Str::of($this->getBirthDate())->trim()->isEmpty();
        $emptyPoly= Str::of($this->getPoly())->trim()->isEmpty();
        $emptyDoctor = Str::of($this->getDoctor())->trim()->isEmpty();
        $emptyDay = Str::of($this->getDay())->trim()->isEmpty();

        return [$emptyPatientName, $emptyBirthDate, $emptyPoly, $emptyDoctor, $emptyDay];
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
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


    public function storeMessageTime()
    {
        $messageReceivedTime = request('messageDateTime');
        $messageReceivedTime = $this->storeSession('messageDateTime', $messageReceivedTime);
        return $messageReceivedTime;
    }

    function sessionMessage()
    {
        // $senderMessage=preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$senderMessage)));
        // return explode("\n",$senderMessage);
        $senderMessage = Str::of($this->senderMessage())->containsAll(['nama', 'ktp', 'lahir(tgl-bln-thn)', 'poli tujuan:']);

        if($senderMessage){
            $sessionMessage = $this->storeSession('senderMessage', strtolower(request('senderMessage')));
            return $sessionMessage;
        }
    }


    public function senderMessage()
    {
       return $this->splitCrash(strtolower(request('body')));
    }

    // public function sendMessage($message)
    // {
    //     return [['message' =>"$message"]];

    // }

    // public function sendMessaageIfEmpty($message)
    // {
    //     if($this->emptyPatientName||$this->emptyBirthDate.$this->emptyPoly.$this->emptyDoctor.$this->emptyDay){

    //         return json_encode([['message' =>"$message"]]);
    //     }
    // }

    public function getContact()
    {
        return request('senderName');
    }

    public function isContactExist()
    {
        $isContactExist = RegPeriksa::select('kontak_wa')->where('kontak_wa', $this->getContact())->first();
        return $isContactExist->kontak_wa;
    }

    public function getPatientName() //ambil nama pasien
    {

        $patientName = Str::between($this->senderMessage(),"nama sesuai ktp:","lahir(tgl-bln-thn):" );
        return ltrim($patientName, ' ');
    }

    public function getBirthDate()
    {
        $birthDate = Str::between($this->senderMessage(), "lahir(tgl-bln-thn):", "poli tujuan:");

        return ltrim($birthDate, ' ');
    }

    public function getPoly()
    {
        $poly = Str::between($this->senderMessage(), "poli tujuan:", "dokter tujuan:");
        return ltrim($poly, ' ');

    }

    public function getDoctor()
    {
        $doctor = Str::between($this->senderMessage(),"dokter tujuan:", "hari");
        return ltrim($doctor, ' ');
    }

    public function getDay()
    {
        $day = trim(Str::after($this->senderMessage(), "hari:"),"\"");
        return ltrim($day, ' ');
    }






}
