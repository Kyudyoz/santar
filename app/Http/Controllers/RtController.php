<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\User;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class RtController extends Controller
{
    public function validasi()
    {
        $surats2 = Surat::where('rt_id', auth()->user()->rt_id)
            ->orderBy('status')
            ->simplePaginate(5);
        return view('rt.validasi', [
            'title' => 'Validasi Surat Pengantar',
            'active' => 'Validasi',
            'surats2' => $surats2,
        ]);
    }

    public function validasiWarga()
    {
        $users = User::where('rt_id', auth()->user()->rt_id)
            ->where('status', 'Menunggu Divalidasi')->latest()->simplePaginate(5);
        $users2 = User::where('rt_id', auth()->user()->rt_id)
            ->where('status', '!=', 'Menunggu Divalidasi')->orderBy('status', 'DESC')->latest()->simplePaginate(5);
        return view('rt.validasi-warga', [
            'title' => 'Validasi Akun Warga',
            'active' => 'Validasi Akun Warga',
            'users' => $users,
            'users2' => $users2,
        ]);
    }

    public function dataWarga()
    {
        return view('rt.data-warga', [
            'title' => 'Data Warga',
            'active' => 'Data Warga',
        ]);
    }

    public function tambahWarga()
    {
        return view('rt.tambah-warga', [
            'title' => 'Data Warga',
            'active' => 'Data Warga',
        ]);
    }

    public function unggahTtd()
    {
        return view('rt.unggah-ttd', [
            'title' => 'Unggah Tanda Tangan',
            'active' => 'Unggah Tanda Tangan',
            'rts' => Rt::where('id', auth()->user()->rt_id)->get(),
        ]);
    }

    public function updateTtd(Request $request)
    {
        $rules = [
            'ttd' => 'image|file|max:2048',
        ];
        $validatedData = $request->validate($rules);
        if ($request->file('ttd')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $validatedData['ttd'] = $request->file('ttd')->store('ttd');
        }
        $id = $request->id;
        $validatedData['id'] = $id;



        Rt::where('id', $id)->update($validatedData);
        return redirect('/unggahTtd')->with('success', 'Tanda Tangan Berhasil Diunggah!');
    }

    public function storeWarga(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|unique:users',
            'nama' => 'required',
            'tanggal_lahir' => 'required',
            'tempat_lahir' => 'required',
            'alamat' => 'required',
            'pekerjaan' => 'required',
            'password' => 'required',
        ]);
        $validatedData['rt_id'] = $request->rt_id;
        $validatedData['password'] = bcrypt($request->password);
        $validatedData['jenis_kelamin'] = $request->jenis_kelamin;
        $validatedData['agama'] = $request->agama;
        $validatedData['status_perkawinan'] = $request->status_perkawinan;
        $validatedData['no_hp'] = $request->no_hp;
        $validatedData['role'] = $request->role;
        User::create($validatedData);
        return redirect('/dataWarga')->with('success', 'Warga Berhasil Ditambah!');
    }

    public function detail($id)
    {
        $id = Crypt::decrypt($id);

        $users = User::where('id', $id)->get();
        return view('rt.detail-warga', [
            'title' => 'Data Warga',
            'active' => 'Data Warga',
            'users' => $users
        ]);
    }

    public function detailValidasi($id)
    {
        $id = Crypt::decrypt($id);

        $users = User::where('id', $id)->get();
        return view('rt.detail-validasi', [
            'title' => 'Detail Validasi',
            'active' => 'Validasi Akun Warga',
            'users' => $users
        ]);
    }

    public function setuju($id)
    {
        $id = Crypt::decrypt($id);

        $surat = Surat::find($id);

        $rt = Rt::find($surat->rt_id);

        if ($rt->ttd) {
            $surat->status = 'Disetujui';

            $surat->save();
            return redirect('/validasi')->with('success', 'Surat Berhasil Disetujui!');
        } else {
            return back()->with('ttdError', 'Anda Belum Mengunggah Tanda Tangan!');
        }
    }

    public function setujuiAkun($id)
    {
        $id = Crypt::decrypt($id);

        $user = User::find($id);

        $user->status = 'Disetujui';

        $user->save();

        $phone_number = $user->no_hp;

        $phone_number = preg_replace('/^62/', '0', $phone_number);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone_number,
                'message' => "Pengajuan registrasi akun anda telah disetujui. \r\nSilahkan Login : https://kelompok3.rsix.site/login",
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: xNs9nSws9bqjaBxD04WQ' //change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return redirect('/validasiWarga')->with('success', 'Akun Berhasil Disetujui!');
    }


    public function tolak($id)
    {
        $id = Crypt::decrypt($id);

        $surat = Surat::find($id);

        $surat->status = 'Ditolak';

        $surat->save();
        return redirect('/validasi')->with('success', 'Surat Berhasil Ditolak!');
    }

    public function tolakAkun($id)
    {
        $id = Crypt::decrypt($id);

        $user = User::find($id);

        $user->status = 'Ditolak';

        $user->save();

        $phone_number = $user->no_hp;

        $phone_number = preg_replace('/^62/', '0', $phone_number);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone_number,
                'message' => "Pengajuan registrasi akun anda ditolak. \r\nHubungi RT setempat melalui : https://kelompok3.rsix.site/infoRt",
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: xNs9nSws9bqjaBxD04WQ' //change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return redirect('/validasiWarga')->with('success', 'Akun Berhasil Ditolak!');
    }
}
