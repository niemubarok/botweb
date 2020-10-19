<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
    protected $visible = ['no_rkm_medis', 'nm_pasien','no_ktp'];

    public function regPeriksa()
    {
        return $this->belongsTo('App\Models\RegPeriksa', 'no_rkm_medis');
    }

    
    
}
