<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    protected $fillable = [
        'no_nota',
        'produk_id',
        'tanggal_terjual',
        'jumlah_terjual',
        'harga_satuan',
        'total_harga',
        'catatan',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    protected static function booted()
    {
        static::creating(function ($penjualan) {
            $produk = Produk::findOrFail($penjualan->produk_id);
            $penjualan->harga_satuan =$produk->harga_jual;
            $penjualan->total_harga = $penjualan->jumlah_terjual * $produk->harga_jual;

            // Kurangi stok_tersedia otomatis saat transaksi disimpan
            if ($produk->stok_tersedia >=$penjualan->jumlah_terjual) {
                $produk->decrement('stok_tersedia',$penjualan->jumlah_terjual);
            }
        });
    }
}