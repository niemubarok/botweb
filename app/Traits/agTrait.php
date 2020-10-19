<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait agTrait
{
    public function reply($pesan)
    {
        return [['type' => 
                            'message', 'data' => [
                            'mode' => 
                            'reply', 'pesan' => 
                            $pesan]]];
    }

    public function replyMedia($caption, $link)
    {
        // return [['type'=>'file','data'=>['mode'=>'chat','pesan'=>$caption,'filetype'=>'image/png','source'=>$source,'name'=>$nama]]];
        $source=base64_encode(file_get_contents($link));
        $result[]=['type'=>'file','data'=>['mode'=>'chat','pesan'=>$caption,'filetype'=>'image/png','source'=>$source,'name'=>'qrcode']];

        return $result;
    }


}