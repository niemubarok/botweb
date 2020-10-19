<?php

namespace App\Traits;
use Twilio\TwiML\MessagingResponse;
use QRcode;

trait responseTrait
{
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
        $message = $response->message($pesan);
        $message->media($link);

        return $response;
    }
}
