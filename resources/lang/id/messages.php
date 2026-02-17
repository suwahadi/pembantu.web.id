<?php

return [
    // Messages dalam Bahasa Indonesia
    'orders' => [
        'created' => 'Pesanan berhasil dibuat',
        'updated' => 'Pesanan berhasil diperbarui',
        'cancelled' => 'Pesanan dibatalkan',
        'completed' => 'Pesanan selesai',
    ],
    'payments' => [
        'initiated' => 'Pembayaran dimulai',
        'pending' => 'Pembayaran tertunda',
        'settled' => 'Pembayaran berhasil',
        'expired' => 'Pembayaran waktu habis',
        'failed' => 'Pembayaran gagal',
    ],
    'disputes' => [
        'opened' => 'Sengketa dibuka',
        'investigating' => 'Sengketa sedang diselidiki',
        'resolved' => 'Sengketa terselesaikan',
        'rejected' => 'Pengajuan ditolak',
    ],
    'refunds' => [
        'queued' => 'Pengembalian dana dalam antrian',
        'processing' => 'Pengembalian dana sedang diproses',
        'paid' => 'Dana telah dikembalikan',
        'failed' => 'Pengembalian dana gagal',
    ],
    'payouts' => [
        'queued' => 'Pencairan dana dalam antrian',
        'processing' => 'Pencairan dana sedang diproses',
        'paid' => 'Dana telah dicairkan',
        'failed' => 'Pencairan dana gagal',
    ],
    'validation' => [
        'required' => ':attribute harus diisi',
        'email' => ':attribute harus berupa email yang valid',
        'confirmed' => 'Konfirmasi :attribute tidak cocok',
        'unique' => ':attribute sudah terdaftar',
        'min' => ':attribute minimal :min karakter',
        'max' => ':attribute maksimal :max karakter',
    ],
];
