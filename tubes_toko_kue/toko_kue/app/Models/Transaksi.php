// app/Models/Transaksi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $guarded = ['id']; // Gunakan guarded agar lebih aman

    // Relasi ke Pembeli (One-to-Many terbalik)
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class);
    }

    // Relasi ke Detail Transaksi (One-to-Many)
    public function details()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}