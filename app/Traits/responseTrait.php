<?php

namespace App\Traits;
use Illuminate\Support\Str;
use App\Models\RegPeriksa;
use Illuminate\Http\Request;
use App\Traits\stringsTrait;
use Twilio\TwiML\MessagingResponse;
use App\Traits\messageTrait;
use Illuminate\Support\Arr;
use QRcode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

const QRCODE_NORAWAT = "http://webnbot.test/storage/qrcode/file.png";


trait responseTrait
{
    // use messageTrait;

    public function sendResponse($message)
    {
        $response = new MessagingResponse();
        $response->message($message);
        return $response;
    }


    public function sendMultipleResponse($message1,$message2,$link)
    {
        $response = new MessagingResponse();
        $response->message($message1);
        // $response->message($message3);
        $message = $response->message($message2);
        $message->media($link);
        return $response;
    }

    public function sendMedia($pesan,$link)
    {
        $response = new MessagingResponse();
        $message = $response->message($pesan);
        $message->media($link);
        return $response;
    }


    public function storeQrCode($content, $fileName)
    {

        $tempDir =public_path('storage/qrcode/');
        $codeContents = $content;
        $file = $fileName.'.png';
        $pngAbsoluteFilePath = $tempDir.$file;
        QRcode ::png($codeContents, $pngAbsoluteFilePath);
        return asset('storage/qrcode/'.$file);

    }

    public function sendQrCode($pesan, $link)
    {
        $response = new MessagingResponse();
        // $message->media('http://webnbot.test/storage/qrcode/'.png');

        $message = $response->message($pesan);
        $message->media($link);

        return $response;

    }


    // public function sendMediaResponse()
    // {

    //     $response = new MessagingResponse();
    //         $message = $response->message("Thanks for the image! Here's one for you!");
    //         $message->media('http://webnbot.test/storage/qrcode/'.png');
    //     return $response;

    // }
}
