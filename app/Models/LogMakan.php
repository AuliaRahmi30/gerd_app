<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMakan extends Model
{
    use HasFactory;

    protected $table = 'log_makans';

    // ================= CONSTANTS =================
    public const STATUS_WAITING = 'menunggu';
    public const STATUS_LATE = 'telat';
    public const STATUS_DONE = 'sudah';
    public const STATUS_LEGACY_WAITING = 'belum';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam',
        'jadwal',
        'status',
        'konfirmasi',
        'jadwal_makan_id',
        'reminder_sent_at',
    ];

    protected $casts = [
        'reminder_sent_at' => 'datetime',
    ];

    // ================= STATUS METHODS =================
    
    public static function allScheduledStatuses(): array
    {
        return [
            self::STATUS_WAITING,
            self::STATUS_LATE,
            self::STATUS_DONE,
            self::STATUS_LEGACY_WAITING,
        ];
    }

    public static function waitingStatuses(): array
    {
        return [
            self::STATUS_WAITING,
            self::STATUS_LEGACY_WAITING,
        ];
    }

    /**
     * Get statuses that indicate a schedule has been processed
     * Digunakan di RunMealScheduler untuk cek duplikasi
     */
    public static function processedStatuses(): array
    {
        return [
            self::STATUS_WAITING,
            self::STATUS_LATE,
            self::STATUS_DONE,
            self::STATUS_LEGACY_WAITING,
        ];
    }

    public static function normalizeStatus(string $status): string
    {
        return $status === self::STATUS_LEGACY_WAITING 
            ? self::STATUS_WAITING 
            : $status;
    }

    // ================= SCOPES =================
    
    public function scopeWaiting($query)
    {
        return $query->whereIn('status', self::waitingStatuses());
    }

    public function scopeToday($query)
    {
        return $query->where('tanggal', now()->toDateString());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeProcessed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DONE,
            self::STATUS_LATE,
        ]);
    }

    // ================= STATUS CHECK METHODS =================
    
    public function isWaiting(): bool
    {
        return in_array($this->status, self::waitingStatuses());
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    public function isProcessed(): bool
    {
        return in_array($this->status, self::allScheduledStatuses());
    }

    // ================= RELATIONSHIPS =================
    
    public function jadwalMakan()
    {
        return $this->belongsTo(JadwalMakan::class, 'jadwal_makan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ================= ACCESSORS =================
    
    public function getKonfirmasiLabelAttribute()
    {
        return match ($this->konfirmasi) {
            'device' => 'Push Button',
            'telegram' => 'Telegram',
            null => '—',
            default => ucfirst($this->konfirmasi),
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_WAITING => '⏳ Menunggu',
            self::STATUS_DONE => '✅ Sudah',
            self::STATUS_LATE => '⚠️ Telat',
            self::STATUS_LEGACY_WAITING => '⏳ Menunggu',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_WAITING => 'badge-warning',
            self::STATUS_DONE => 'badge-success',
            self::STATUS_LATE => 'badge-danger',
            self::STATUS_LEGACY_WAITING => 'badge-warning',
            default => 'badge-secondary',
        };
    }
}