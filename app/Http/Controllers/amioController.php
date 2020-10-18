<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\amioTrait;

class amioController extends Controller
{


    use amioTrait;

    public function __invoke(Request $request)
    {
        $message= "ini adalah pesan dari laravel";

        return $this->sendResponseToAmio('text', $message);
    }
}

