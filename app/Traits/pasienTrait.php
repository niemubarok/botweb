<?php

namespace App\Traits;

use App\Traits\messageTrait;
use App\Models\Pasien;
use App\Traits\timeTrait;

trait pasienTrait
{
    use messageTrait;
    use timeTrait;

    public function pasien()
    {
         //ambil nama pasien
        $noKtp      = $this->getNoKtp();
        $nmPasien   = $this->getNamaPasien();
        $tglLahir   = $this->tglLahir();

        $pasien = Pasien::where('no_rkm_medis', $noKtp)//no. rekam medis
                    ->orWhere('no_ktp', $noKtp)
                    ->orWhere(function($query)use($tglLahir, $nmPasien){
                        $query->where('tgl_lahir', $tglLahir)->Where('nm_pasien', 'like',"%$nmPasien%");
                        })->first();

        return $pasien;
    }

    public function nik()
    {
        return $this->pasien()->no_ktp;
    }

    public function noRm()
    {
        return $this->pasien()->no_rkm_medis;
    }

    public function ttl()
    {
        return $this->senderMessage()[2];
    }


}
