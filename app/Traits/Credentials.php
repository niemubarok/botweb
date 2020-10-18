<?php

namespace App\Traits;
use Twilio\Rest\Client;

trait Credentials
{
    private $sid = "AC2ea57b77607e5762d0f3e455ce15cadc",
            $token="348e24c9f2975d44eebb22aa43afc612";

    public function twilioCredential()
    {
        // $this->sid = "AC2ea57b77607e5762d0f3e455ce15cadc";
        // $this->token= "348e24c9f2975d44eebb22aa43afc612";
        return $client = new Client($this->sid, $this->token);

    }
}
