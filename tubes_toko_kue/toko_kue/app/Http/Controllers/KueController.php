<?php

namespace App\Http\Controllers;

use App\Models\Kue; // Pastikan Anda mengimpor Model Kue
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; // Controller dasar Laravel
Route::resource('kues', KueController::class);

class KueController extends Controller
{
    /**
     * Menampilkan daftar semua kue. (READ)
     */
    public function index()
    {
        // Mengambil semua data dari tabel 'kues'
        $kues = Kue::all(); 
        
        // Mengarahkan ke view 'kues.index' dan mengirim data $kues
        return view('kues.index', compact('kues'));
    }

    /**
     * Menampilkan form untuk membuat kue baru. (CREATE)
     */
    public function create()
    {
        // Mengarahkan ke view 'kues.create' yang berisi form input
        return view('kues.create');
    }

    /**
     * Menyimpan kue baru ke database. (STORE)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_kue' => 'required|string|max:255|unique:kues,nama_kue', // Harus unik
            'harga' => 'required|integer|min:1000', // Harga harus berupa angka, minimal 1000
        ], [
            // Pesan error kustom
            'nama_kue.required' => 'Nama Kue wajib diisi.',
            'nama_kue.unique' => 'Nama Kue ini sudah terdaftar.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.integer' => 'Harga harus berupa angka bulat.',
            'harga.min' => 'Harga minimal adalah Rp 1.000.',
        ]);

        // 2. Simpan Data ke Database
        Kue::create($request->all());

        // 3. Redirect dengan pesan sukses
        return redirect()->route('kues.index')
                         ->with('success', 'Kue baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu kue. (SHOW)
     * Opsional, tidak selalu digunakan dalam CRUD sederhana.
     */
    public function show(Kue $kue)
    {
        return view('kues.show', compact('kue'));
    }

    /**
     * Menampilkan form untuk mengedit kue. (EDIT)
     */
    public function edit(Kue $kue)
    {
        // Mengarahkan ke view 'kues.edit' dan mengirim objek $kue
        return view('kues.edit', compact('kue'));
    }

    /**
     * Memperbarui kue yang ada di database. (UPDATE)
     */
    public function update(Request $request, Kue $kue)
    {
        // 1. Validasi Input (kecuali unique, harus diabaikan untuk ID saat ini)
        $request->validate([
            'nama_kue' => 'required|string|max:255|unique:kues,nama_kue,'.$kue->id, 
            'harga' => 'required|integer|min:1000',
        ]);
        
        // 2. Update Data
        $kue->update($request->all());

        // 3. Redirect dengan pesan sukses
        return redirect()->route('kues.index')
                         ->with('success', 'Data Kue berhasil diperbarui.');
    }

    /**
     * Menghapus kue dari database. (DELETE)
     */
    public function destroy(Kue $kue)
    {
        // Hapus data kue
        $kue->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('kues.index')
                         ->with('success', 'Kue berhasil dihapus.');
    }
}