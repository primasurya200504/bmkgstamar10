<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guideline_id',
        'application_number',
        'type',
        'documents',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'notes'
    ];

    protected $casts = [
        'documents' => 'array',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // ADDED: Additional attributes untuk dashboard
    protected $appends = [
        'status_label',
        'type_label',
        'date_range_display',
        'duration_days'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guideline()
    {
        return $this->belongsTo(Guideline::class);
    }

    public function histories()
    {
        return $this->hasMany(ApplicationHistory::class)->orderBy('created_at', 'desc');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function archive()
    {
        return $this->hasOne(Archive::class);
    }

    // ENHANCED: Method logHistory dengan error handling
    public function logHistory($action, $actor_type, $actor_id, $title, $description = null, $metadata = null)
    {
        try {
            // Check if ApplicationHistory model exists
            if (!class_exists('\App\Models\ApplicationHistory')) {
                \Log::warning('ApplicationHistory model not found, skipping history log');
                return null;
            }

            return $this->histories()->create([
                'action' => $action,
                'actor_type' => $actor_type,
                'actor_id' => $actor_id,
                'title' => $title,
                'description' => $description,
                'metadata' => $metadata,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log application history: ' . $e->getMessage());
            return null;
        }
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'payment_pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Bayar',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getTypeLabelAttribute()
    {
        return $this->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
    }

    // ADDED: Additional computed attributes
    public function getDateRangeDisplayAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'Tanggal tidak tersedia';
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
    }

    public function getDurationDaysAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }

    // ADDED: Method untuk determine data type
    public function getDataTypeAttribute()
    {
        $today = now()->format('Y-m-d');

        if ($this->end_date < '1990-01-01') {
            return 'historical';
        } elseif ($this->start_date >= '1990-01-01' && $this->end_date <= $today) {
            return 'available';
        } elseif ($this->start_date > $today) {
            return 'future';
        } else {
            return 'mixed';
        }
    }

    // ADDED: Scope untuk filter berdasarkan tanggal
    public function scopeHistoricalData($query)
    {
        return $query->where('end_date', '<', '1990-01-01');
    }

    public function scopeAvailableData($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '>=', '1990-01-01')
            ->where('end_date', '<=', $today);
    }

    public function scopeFutureData($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '>', $today);
    }

    public function scopeMixedData($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('start_date', '<=', $today)
            ->where('end_date', '>', $today);
    }

    // ADDED: Method untuk get document info
    public function getDocumentSummary()
    {
        if (!$this->documents || !is_array($this->documents)) {
            return [
                'count' => 0,
                'total_size' => 0,
                'types' => []
            ];
        }

        $totalSize = array_sum(array_column($this->documents, 'size'));
        $types = array_unique(array_column($this->documents, 'type'));

        return [
            'count' => count($this->documents),
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'types' => $types
        ];
    }

    // ADDED: Method untuk validate dates
    public function validateDateRange()
    {
        if (!$this->start_date || !$this->end_date) {
            return [
                'valid' => false,
                'message' => 'Tanggal mulai dan selesai harus diisi'
            ];
        }

        if ($this->start_date > $this->end_date) {
            return [
                'valid' => false,
                'message' => 'Tanggal selesai harus sama atau setelah tanggal mulai'
            ];
        }

        $diffDays = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));

        if ($diffDays > 3650) { // 10 years
            return [
                'valid' => true,
                'warning' => 'Rentang waktu sangat panjang (lebih dari 10 tahun)'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Rentang tanggal valid'
        ];
    }
}
