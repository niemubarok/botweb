<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;


trait amioTrait
{
    private $url = "https://api.amio.io/v1/messages";

    public function event()
    {
        return request('event');
    }


    public function ChannelId()
    {
        return request('data.channel.id');
    }

    public function ContactId()
    {
        return request('data.contact.id');
    }

    public function SenderMessage()
    {
        return request('data.content.payload');
    }

    public function messageType()
    {
        return request('data.content.type');
    }

    public function sendResponseToAmio($type, $message)
    {
        $res = [
            "channel"=> [
            "id"=> $this->ChannelId()
            ],
            "contact"=> [
            "id"=> $this->contactId()
            ],
            "content"=> [
            "type"=> $type,
            "payload"=> $message
            ]
            ];
        if($this->event() == 'message_received' && $this->senderMessage()== 'hai'){
        $response = Http::withToken('pn77BlM4gxoCZmvUq4jBGG5diEzQVDZiCiuxsf2DMuNrM66EzUfmR1NWNxLqXBbIw2gqkkmKkMMQbb3jrT2Ol7Gx9b')->post($this->url, $res);

        return $response->json();
        }else{
            echo "error";
        }
    }


}
