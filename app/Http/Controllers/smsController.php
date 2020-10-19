<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class smsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $isiSms = [
            "result"=> true,
            "data"=> [
              "phone"=> "085524914191",
              "vendor"=> "RANDOM"
            ],
            "message"=> "SMS Successfully Sent."
        ];

        $url = "https://api.atlantic-group.id/tools/sms";

        // dd($isiSms);
        $sms = Http::withToken('aR0Ml5mP3xdxUHRVdggeT6lpsop7KhV0')->post($url, $isiSms);
        return response($sms);
    }
}
