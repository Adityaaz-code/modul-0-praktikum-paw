<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pembeli;
use App\Models\Kue; // Diperlukan untuk validasi stok kue
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Diperlukan untuk Database Transaction

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar semua Transaksi. (READ)
     */
    public function index()
    {
        // Ambil semua transaksi dan muat relasi pembeli
        $transaksis = Transaksi::with('pembeli')->orderBy('tanggal_transaksi', 'desc')->get();
        return view('transaksis.index', compact('transaksis'));
    }

    /**
     * Menampilkan form untuk membuat Transaksi baru. (CREATE)
     */
    public function create()
    {
        // Mengirim data Kue dan Pembeli untuk dipilih di form
        $kues = Kue::all();
        $pembelis = Pembeli::all();
        return view('transaksis.create', compact('kues', 'pembelis'));
    }

    /**
     * Menyimpan Transaksi baru ke database (Header dan Detail). (STORE)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'pembeli_id' => 'required|exists:pembelis,id',
            'tanggal_transaksi' => 'required|date', // Anda bisa menggunakan date_format jika ada jam
            'items' => 'required|array|min:1', // Harus ada minimal 1 item
            'items.*.kue_id' => 'required|exists:kues,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $details = [];

            // 2. Simpan Header Transaksi (dengan total_harga sementara 0)
            $transaksi = Transaksi::create([
                'pembeli_id' => $validatedData['pembeli_id'],
                'tanggal_transaksi' => $validatedData['tanggal_transaksi'],
                'total_harga' => 0, // Akan di-update setelah total dihitung
            ]);

            // 3. Simpan Detail Transaksi dan Hitung Total
            foreach ($validatedData['items'] as $item) {
                $kue = Kue::find($item['kue_id']);
                $hargaSatuan = $kue->harga; // Ambil harga kue saat ini
                $subtotal = $item['jumlah'] * $hargaSatuan;
                $totalHarga += $subtotal;
                
                $details[] = new DetailTransaksi([
                    'kue_id' => $item['kue_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $hargaSatuan, // Simpan harga saat transaksi terjadi
                    'subtotal' => $subtotal,
                ]);
            }

            // Simpan semua detail sekaligus dengan relasi
            $transaksi->details()->saveMany($details);

            // 4. Update Total Harga di Header Transaksi
            $transaksi->update(['total_harga' => $totalHarga]);

            DB::commit(); // Semua berhasil, simpan ke database

            return redirect()->route('transaksis.index')
                             ->with('success', 'Transaksi berhasil dicatat dengan total Rp ' . number_format($totalHarga, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika terjadi error
            return back()->withInput()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail satu Transaksi beserta item-itemnya.
     */
    public function show(Transaksi $transaksi)
    {
        // Muat relasi Pembeli dan Detail Transaksi (beserta info Kue-nya)
        $transaksi->load('pembeli', 'details.kue'); 
        return view('transaksis.show', compact('transaksi'));
    }

    /**
     * Fungsi Edit, Update, dan Destroy biasanya dihindari untuk transaksi yang sudah selesai
     * demi menjaga integritas data keuangan. Jika diperlukan, logikanya akan mirip
     * dengan KueController, tetapi memerlukan penanganan detail yang lebih rumit.
     * Untuk tujuan praktikum, cukup buat function-nya jika diperlukan resource route lengkap.
     */
    public function edit(Transaksi $transaksi)
    {
        // Logika edit transaksi yang rumit (biasanya tidak diizinkan)
        return back()->with('error', 'Edit transaksi tidak diizinkan untuk menjaga integritas data.');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        // Logika update transaksi
        return back()->with('error', 'Update transaksi tidak diizinkan.');
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::beginTransaction();
        try {
            // Penting: Hapus dulu detailnya karena ada foreign key
            $transaksi->details()->delete(); 
            
            // Hapus header transaksi
            $transaksi->delete();
            DB::commit();
            
            return redirect()->route('transaksis.index')
                             ->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}