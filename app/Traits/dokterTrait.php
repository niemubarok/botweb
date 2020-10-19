<?php

namespace App\Traits;

use App\Models\JadwalDokter;
use Illuminate\Support\Str;
use App\Models\Dokter;
use App\Traits\timeTrait;
use App\Traits\messageTrait;
use app\Traits\poliTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait dokterTrait
{
    use messageTrait;
    use timeTrait;
    use poliTrait;

    public function dokter()
    {
        $dokterTujuan   = $this->getDokter();
        $dokterTujuan   = Dokter::where('nm_dokter', 'LIKE', "%$dokterTujuan%")->first();
        return $dokterTujuan;

    }

    public function kodeDokter()
    {
        $kodeDokter = $this->dokter()['kd_dokter'];
        return $kodeDokter;
    }

    

}
