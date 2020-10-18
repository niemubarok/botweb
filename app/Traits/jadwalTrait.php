<?php

namespace App\Traits;
use App\Models\JadwalDokter;
use App\Models\Dokter;
use app\Traits\poliTrait;
use App\Traits\dokterTrait;
use App\Traits\timeTrait;
use App\Traits\messageTrait;

trait jadwalTrait
{
    use dokterTrait;
    use poliTrait;
    use timeTrait;
    use messageTrait;

    public function jadwal()
    {
        return JadwalDokter::all();
        // return
    }



    public function namaDokterDariJadwal()
    {
         //dokter tujuan jika ditemukan di jadwal dokter
         $jadwalPerDokter= JadwalDokter::where('kd_poli', $this->kodePoli())->where('kd_dokter', $this->kodeDokter())->where('hari_kerja', $this->dayToHari())->first();

         $dokterTujuan = Dokter::where('kd_dokter', $jadwalPerDokter->kd_dokter)->first();
         $nmDokterDariJadwal=  $dokterTujuan->nm_dokter;
         return $nmDokterDariJadwal;
    }

    public function jadwalPerPoli($i)
    {
         //mencari jadwal dokter jika pasien mengetikan nama dokter tidak sesuai spesialisnya.
        $jadwalPerPoli= JadwalDokter::where('kd_poli', $this->kodePoli())->where('hari_kerja', $this->dayToHari())->join('dokter', 'dokter.kd_dokter','=', 'jadwal.kd_dokter')->orderBy('hari_kerja')->get();

        $jadwal = collect($jadwalPerPoli);

        $namaDokter = $jadwal[$i]['nm_dokter'];
        $jamMulai   = $jadwal[$i]['jam_mulai'];
        $jamSelesai = $jadwal[$i]['jam_selesai'];
        $hariKerja  = $this->dayToHari();

        $jadwalPerPoli  = "-".$namaDokter. " (".$jamMulai."-".$jamSelesai.")";
        return $jadwalPerPoli;
    }

    public function jamMulaiPraktek()
    {
        $jamPraktek = JadwalDokter::select('jam_mulai', 'jam_selesai')->where('kd_dokter', $this->kodeDokter())->where('hari_kerja', $this->dayTohari())->first();

        return $jamPraktek->jam_mulai;
    }

    public function jamSelesaiPraktek()
    {
        $jamPraktek = JadwalDokter::select('jam_mulai', 'jam_selesai')->where('kd_dokter', $this->kodeDokter())->where('hari_kerja', $this->dayTohari())->first();

        return $jamPraktek->jam_Selesai;


    }
}
