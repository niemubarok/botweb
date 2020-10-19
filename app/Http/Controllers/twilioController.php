<?php

namespace App\Http\Controllers;

use App\Models\JadwalDokter;
use App\Models\Poli;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;
use App\Traits\Credentials;
use App\Traits\dokterTrait;
use App\Traits\jadwalTrait;
use App\Traits\messageTrait;
use App\Traits\pasienTrait;
use App\Traits\poliTrait;
use App\Traits\registrationTrait;
use App\Traits\responseTrait;
use App\Traits\timeTrait;
use Illuminate\Support\Facades\Storage;
use QRcode;

include(app_path('phpqrcode/qrlib.php'));


class twilioController extends Controller
{
    use Credentials;
    use responseTrait;
    use messageTrait;
    use poliTrait;
    use timeTrait;
    use dokterTrait;
    use registrationTrait;
    use pasienTrait;
    use jadwalTrait;

    public function responseMessage(Request $request)
    {
        // return $this->storeQrCode('konten', 'file2');
        // return $this->sendQrCode("http://regbot.pagekite.me/webnbot/public/storage/qrcode/file2.png");
        return $this->registration();

        // dd();



    }


    public function createMessage()
    {
        // $sid = "AC2ea57b77607e5762d0f3e455ce15cadc";
        // $token = "348e24c9f2975d44eebb22aa43afc612";
        // $client = new Client($sid, $token);
        // $clientNumber =

        // $response= $client->messages->create(
        //     // $clientNumber,
        //     "whatsapp:+6285945035196",
        //     [
        //         "from"=> "whatsapp:+14155238886",
        //         "body" => "tes from laravel"
        //     ]
        //     );
        // dd($this->twilioCredential());

    }
}
