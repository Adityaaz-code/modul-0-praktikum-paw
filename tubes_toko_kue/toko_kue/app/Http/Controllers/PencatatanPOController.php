<?php

namespace App\Http\Controllers;

use App\Models\PencatatanPO;
use App\Models\DetailPencatatanPO;
use App\Models\BahanBaku; // Diperlukan untuk update stok
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Diperlukan untuk Database Transaction

class PencatatanPOController extends Controller
{
    /**
     * Menampilkan daftar semua Pencatatan PO.
     */
    public function index()
    {
        // Ambil semua PO dan urutkan dari yang terbaru
        $pos = PencatatanPO::orderBy('tanggal_pesan', 'desc')->get();
        return view('pencatatan_pos.index', compact('pos'));
    }

    /**
     * Menampilkan form untuk membuat PO baru.
     */
    public function create()
    {
        // Mengirim data BahanBaku untuk dipilih di form
        $bahanBakus = BahanBaku::all();
        return view('pencatatan_pos.create', compact('bahanBakus'));
    }

    /**
     * Menyimpan PO baru ke database (Header dan Detail) dengan Database Transaction.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedHeader = $request->validate([
            'supplier' => 'required|string|max:255',
            'tanggal_pesan' => 'required|date',
            'items' => 'required|array|min:1', // Harus ada minimal 1 item
            'items.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'items.*.jumlah_beli' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:1',
        ]);

        // Menggunakan DB Transaction: Jika salah satu proses gagal, semua dibatalkan
        DB::beginTransaction();
        try {
            // 2. Simpan Header PO
            $po = PencatatanPO::create([
                'supplier' => $validatedHeader['supplier'],
                'tanggal_pesan' => $validatedHeader['tanggal_pesan'],
                'status' => 'pending', 
                'total_biaya' => 0, // Akan dihitung dan di-update
            ]);

            $totalBiaya = 0;
            $details = [];

            // 3. Simpan Detail PO dan Hitung Total Biaya
            foreach ($validatedHeader['items'] as $item) {
                $subtotal = $item['jumlah_beli'] * $item['harga_satuan'];
                $totalBiaya += $subtotal;
                
                $details[] = new DetailPencatatanPO([
                    'bahan_baku_id' => $item['bahan_baku_id'],
                    'jumlah_beli' => $item['jumlah_beli'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal,
                ]);
            }
            
            // Simpan semua detail sekaligus dengan relasi
            $po->details()->saveMany($details);

            // 4. Update Total Biaya di Header PO
            $po->update(['total_biaya' => $totalBiaya]);

            DB::commit(); // Semua berhasil, simpan ke database

            return redirect()->route('pencatatan_pos.index')
                             ->with('success', 'Pencatatan PO berhasil dibuat dengan total biaya Rp ' . number_format($totalBiaya, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika terjadi error
            return back()->withInput()->with('error', 'Gagal membuat PO. Pastikan semua data item terisi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail satu PO beserta item-itemnya.
     */
    public function show(PencatatanPO $pencatatan_po)
    {
        // Menggunakan load('details.bahanBaku') untuk memuat detail dan nama bahan baku
        $pencatatan_po->load('details.bahanBaku'); 
        return view('pencatatan_pos.show', ['po' => $pencatatan_po]);
    }

    /**
     * Metode Kustom: Menyelesaikan PO dan memperbarui stok Bahan Baku.
     */
    public function complete(PencatatanPO $pencatatan_po)
    {
        if ($pencatatan_po->status !== 'completed') {
            
            DB::beginTransaction();
            try {
                // 1. Update status PO menjadi 'completed'
                $pencatatan_po->update(['status' => 'completed']);

                // 2. Update Stok Bahan Baku
                foreach ($pencatatan_po->details as $detail) {
                    $bahanBaku = BahanBaku::find($detail->bahan_baku_id);
                    // Menambahkan stok
                    $bahanBaku->stok += $detail->jumlah_beli; 
                    $bahanBaku->save();
                }

                DB::commit();
                return back()->with('success', 'PO berhasil diselesaikan dan stok bahan baku telah diperbarui!');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menyelesaikan PO: ' . $e->getMessage());
            }
        }
        return back()->with('info', 'PO sudah selesai dan stok telah diperbarui sebelumnya.');
    }


    /**
     * Menampilkan form edit (CRUD Update).
     * Catatan: Dalam sistem nyata, PO yang sudah selesai/diproses TIDAK boleh diedit.
     */
    public function edit(PencatatanPO $pencatatan_po)
    {
        if ($pencatatan_po->status == 'completed') {
             return redirect()->route('pencatatan_pos.show', $pencatatan_po)
                              ->with('error', 'PO yang sudah selesai tidak dapat diedit.');
        }
        $bahanBakus = BahanBaku::all();
        $pencatatan_po->load('details');
        return view('pencatatan_pos.edit', ['po' => $pencatatan_po, 'bahanBakus' => $bahanBakus]);
    }

    /**
     * Memperbarui PO yang ada di database.
     */
    public function update(Request $request, PencatatanPO $pencatatan_po)
    {
        // Validasi, mirip dengan store
        // ... (Anda bisa melengkapi validasi di sini)
        
        // Logika update (sedikit lebih kompleks, melibatkan delete/create detail lama)
        // ...
        
        return redirect()->route('pencatatan_pos.index')
                         ->with('success', 'Data PO berhasil diperbarui.');
    }

    /**
     * Menghapus PO dari database.
     */
    public function destroy(PencatatanPO $pencatatan_po)
    {
        // Penting: Hapus dulu detailnya karena ada foreign key
        $pencatatan_po->details()->delete(); 
        
        // Hapus header PO
        $pencatatan_po->delete();

        return redirect()->route('pencatatan_pos.index')
                         ->with('success', 'Pencatatan PO berhasil dihapus.');
    }
}