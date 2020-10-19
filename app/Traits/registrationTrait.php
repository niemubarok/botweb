<?php

namespace App\Traits;

use App\Models\Poli;
use App\Models\RegPeriksa;
use Illuminate\Support\Str;
use App\Traits\dokterTrait;
use App\Traits\messageTrait;
use App\Traits\pasienTrait;
use App\Traits\poliTrait;
use App\Traits\responseTrait;
use App\Traits\timeTrait;
use App\Traits\agTrait;
use App\Traits\jadwalTrait;

trait registrationTrait
{
    use Credentials;
    use responseTrait;
    use messageTrait;
    use poliTrait;
    use timeTrait;
    use dokterTrait;
    use pasienTrait;
    use agTrait;
    use jadwalTrait;

    public function registration()
    {

        $tglLahirFromMessage    = $this->getBirthDate();
        $pasien                 = $this->pasien();
        $tglLahir               = $this->tglLahir();
        $noKtpDariPesan         = $this->getNoKtp();
        $noKtp                  = $this->nik();
        $noRm                   = $this->noRm();
        $poliSesuaiJadwal       = $this->poliSesuaiJadwal();
        $poli                   = $this->poli()->nm_poli;
        $kodePoli               = $this->kodePoli();
        $listPoli               = $this->listPoli();
        $dokter                 = $this->namaDokterDariJadwal();
        $kodeDokter             = $this->kodeDokter();
        $hari                   = $this->dayToHari();
        $tglRegistrasi          = $this->hariKeTgl();
        $jamMulai               = $this->jamMulaiPraktek();
        $jamSelesai             = $this->jamSelesaiPraktek();
        // $senderMessage          = Str::of($this->senderMessage())->containsAll(['nama', 'ktp', 'lahir', 'dokter', 'poli']);


        $regPeriksa             = RegPeriksa::select('no_rawat', 'kd_dokter', 'no_reg', 'kd_poli', 'no_rkm_medis')->where('no_rkm_medis', $noRm)->where('tgl_registrasi', $tglRegistrasi)->where('kd_dokter', $kodeDokter)->where('kd_poli', $kodePoli)->first();

        // dd($regPeriksa);

        $emptyNamaPasien    = Str::of($this->getNamaPasien())->trim()->isEmpty();
        $emptyBirthDate     = Str::of($this->getBirthDate())->trim()->isEmpty();
        $emptyPoli          = Str::of($this->getPoli())->trim()->isEmpty();
        $emptyDokter        = Str::of($this->getDokter())->trim()->isEmpty();
        $emptyDay           = Str::of($this->getDayFromMessage())->trim()->isEmpty();

        if ($emptyNamaPasien == true || $emptyBirthDate == true || $emptyPoli == true || $emptyDokter == true || $emptyDay == true) {
            return json_encode($this->messageValidation());
        } else {
            if (!isset($pasien)) {
                if ($noKtpDariPesan == null) {

                    echo $this->reply("Mohon maaf kami tidak dapat menemukan data atas nama : *{$this->getNamaPasien()}* dengan tanggal lahir *$tglLahirFromMessage*. Silahkan ulangi dan masukan no KTP/ no RM pada form di atas.");
                    // \nHubungi Customer care kami di nomor berikut: (021)-7270681")
                } else if (Str::length($noKtpDariPesan) < 5) {
                    echo $this->reply("Mohon maaf kami tidak dapat menemukan data atas nama : *{$this->getNamaPasien()}* dengan tanggal lahir *$tglLahirFromMessage*. Mohon cek kembali no KTP/no RM/tgl. lahir anda.");
                } else {
                    echo $this->reply("Mohon maaf kami tidak dapat menemukan data atas nama : *{$this->getNamaPasien()}* dengan tanggal lahir *$tglLahirFromMessage*.\nUntuk pasien baru silahkan mendaftar di link berikut.\nHubungi Customer care kami di nomor berikut: (021)-7270681");
                }
            } else if (!isset($poli)) {
                echo $this->reply("Mohon cek kembali nama poli yang anda tuju, berikut nama poli yang tersedia:\n-" . implode("\n-", $listPoli));
            } else if (!isset($poliSesuaiJadwal)) {
                echo $this->reply("Mohon maaf hari $hari tidak ada jadwal $poli.\nBerikut jadwal poli $poli:\n{$this->jadwalPoli()}");
            } else if (!isset($dokter)) {
                echo $this->reply("Mohon maaf hari $hari tidak ada dokter a/n. {$this->getDokter()}\nBerikut jadwal " . $poli . " hari " . $hari . ":\n" . $this->jadwalPerPoli(0) . "\n" . $this->jadwalPerPoli(1));
            } else if (!empty($regPeriksa)) {


                return $this->reply("Alhamdulillah! \n*$hari($tglRegistrasi)* anda sudah terdaftar ke *$poli* dengan *{$regPeriksa->dokter['nm_dokter']}*. Praktek pkl. $jamMulai - $jamSelesai dapat antrian no. *{$regPeriksa['no_reg']}* \nSegera informasikan kepada kami jika anda berhalangan hadir. \nTerimakasih.");
            } else {

                //  ambil no rawat terakhir
                $no_rawat_akhir = RegPeriksa::where('tgl_registrasi', $tglRegistrasi)->max('no_rawat');
                $no_urut_rawat = substr($no_rawat_akhir, 11, 6);
                $tgl_reg_no_rawat = date('Y/m/d', strtotime($tglRegistrasi));
                $no_rawat = $tgl_reg_no_rawat . '/' . sprintf('%06s', ($no_urut_rawat + 1));
                // $qrcode = preg_replace('/\//', '', $no_rawat);
                $qrcode = $this->storeQrCode($noRm, $noRm);

                //mencari no reg terakhir
                $no_reg = RegPeriksa::where('kd_dokter', $this->kodeDokter())->where('tgl_registrasi', $tglRegistrasi)->max('no_reg');

                //  $no_urut_reg = substr($no_reg, 0, 3);
                $no_reg = sprintf('%03s', ($no_reg + 1));


                $biaya_reg = Poli::select('registrasilama')->where('kd_poli', $this->kodePoli())->first();
                $biaya_reg = $biaya_reg['registrasilama'];

                //menentukan umur sekarang
                $birthDate = $pasien->tgl_lahir;
                $birthDate = explode('-', $birthDate);
                $y = $birthDate[0];
                $cy = date('Y');
                $umur = $cy - $y;

                //jam registrasi
                $jam = date('H:i:s');

                $store = RegPeriksa::create([
                    'no_reg'          => $no_reg,
                    'no_rawat'        => $no_rawat,
                    'tgl_registrasi'  => $tglRegistrasi,
                    'jam_reg'         => $jam,
                    'kd_dokter'       => $this->kodeDokter(),
                    'no_rkm_medis'    => $pasien->no_rkm_medis,
                    'kd_poli'         => $this->kodePoli(),
                    'p_jawab'         => $pasien['namakeluarga'],
                    'almt_pj'         => $pasien['alamat'],
                    'hubunganpj'      => $pasien['keluarga'],
                    'biaya_reg'       => $biaya_reg,
                    'stts'            => 'Belum',
                    'stts_daftar'     => 'Lama',
                    'status_lanjut'   => 'Ralan',
                    'kd_pj'           => $pasien['kd_pj'],
                    'umurdaftar'      => $umur,
                    'sttsumur'        => 'Th',
                    'status_bayar'    => 'Belum Bayar'

                ]);
                $store->save();
                $response = $this->replyMedia(
                    "Anda sudah terdaftar\n\n--Detail Pendaftaran--\nNama : *$pasien->nm_pasien*\nNo. RM: *$pasien->no_rkm_medis*\nPoli Tujuan : *$poli*\nDokter: *$dokter*\nTanggal Periksa: *$tglRegistrasi*\njam praktek : pkl. $jamMulai s/d $jamSelesai.\nNo. Antrian : *$no_reg*".
                        "\n\n*Datanglah sesuai jam praktek dokter.* \n\nTunjukan pesan ini kepada petugas pendaftaran di Lobby utama. \nTerimakasih telah mempercayakan kesehatan keluarga anda di RS Ali Sibroh Malisi. \nSemoga lekas sembuh",
                    $qrcode
                );

                return $response;
            }
        }
    }
}
