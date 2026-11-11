<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $fillable = [
        'kategori_id',
        'kode_menu',
        'nama_produk',
        'harga_jual',
        'stok_tersedia',
        'batas_minimum_stok',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    // Indikator status stok otomatis: Tersedia, Menipis, atau Habis
    public function getStatusStokAttribute(): string
    {
        if ($this->stok_tersedia <= 0) {
            return 'Habis';
        }
        if ($this->stok_tersedia <=$this->batas_minimum_stok) {
            return 'Menipis';
        }
        return 'Tersedia';
    }
}