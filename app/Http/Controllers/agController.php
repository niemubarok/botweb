<?php

namespace App\Http\Controllers;

use App\Traits\agTrait;
use Illuminate\Http\Request;
use App\Classes\WhatsATL;
use App\Traits\messageTrait;
use App\Traits\registrationTrait;
include(app_path('phpqrcode/qrlib.php'));

class agController extends Controller
{
    use agTrait;
    use messageTrait;
    use registrationTrait;
    

    public function createMessage(Request $request)
    {
       return $this->registration();
    }
}
