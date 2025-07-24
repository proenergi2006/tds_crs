<?php

namespace App\Http\Controllers;

use App\Models\AttachmentHargaDasar;
use Illuminate\Http\Request;

class AttachmentHargaDasarController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $q = AttachmentHargaDasar::query();
        if ($s = $request->query('search')) {
            $q->where('catatan', 'like', "%{$s}%");
        }
        return response()->json($q->paginate($perPage));
    }

    public function show($id)
    {
        $item = AttachmentHargaDasar::findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'periode_awal'    => ['required','date'],
            'periode_akhir'   => ['required','date','after_or_equal:periode_awal'],
            'catatan'         => ['nullable','string'],
            'lampiran'        => ['required','file','mimes:pdf','max:2048'], // max 2MB
        ]);
        // simpan PDF di storage/app/public/attachment_harga_dasar
        $path = $request->file('lampiran')->store('attachment_harga_dasar','public');
        $data['lampiran']      = $path;
        $data['created_time']  = now();
        $data['created_by']    = $request->user()->name;
        $model = AttachmentHargaDasar::create($data);
        return response()->json($model, 201);
    }
    
    public function update(Request $request, $id)
    {
        $model = AttachmentHargaDasar::findOrFail($id);
        $data = $request->validate([
            'periode_awal'    => ['required','date'],
            'periode_akhir'   => ['required','date','after_or_equal:periode_awal'],
            'catatan'         => ['nullable','string'],
            'lampiran'        => ['nullable','file','mimes:pdf','max:2048'],
        ]);
        if ($request->hasFile('lampiran')) {
            // hapus file lama jika ada
            \Storage::disk('public')->delete($model->lampiran);
            $data['lampiran'] = $request->file('lampiran')->store('attachment_harga_dasar','public');
        }
        $data['lastupdate_time'] = now();
        $data['lastupdate_by']   = $request->user()->name;
        $model->update($data);
        return response()->json($model);
    }
    public function destroy($id)
    {
        AttachmentHargaDasar::destroy($id);
        return response()->json(null, 204);
    }
}
