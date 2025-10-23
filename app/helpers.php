<?php

if (!function_exists('safe_json_decode')) {
    /**
     * Safely decode JSON with fallback
     */
    function safe_json_decode($value, $default = [])
    {
        if (is_null($value)) {
            return $default;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : $default;
        }

        return $default;
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency for Indonesian Rupiah
     */
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('get_status_text')) {
    /**
     * Get status text in Indonesian
     */
    function get_status_text($status)
    {
        $statusTexts = [
            'pending' => 'Menunggu Review',
            'Diproses' => 'Menunggu Upload e-Billing',
            'verified' => 'Terverifikasi',
            'payment_pending' => 'Menunggu Pembayaran',
            'proof_uploaded' => 'Bukti Pembayaran Diupload - Menunggu Verifikasi',
            'paid' => 'Sudah Bayar',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $statusTexts[$status] ?? ucfirst($status);
    }
}
