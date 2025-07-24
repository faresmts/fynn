<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });
    }
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'source',
        'description',
        'value',
        'is_receipt',
        'is_debt',
        'is_fixed',
        'is_variable',
        'is_seasonal',
    ];

    public function scopeIsReceipt(Builder $query): Builder
    {
        return $query->where('is_receipt', true);
    }

    public function scopeIsDebt(Builder $query): Builder
    {
        return $query->where('is_debt', true);
    }

    public function scopeIsFixed(Builder $query): Builder
    {
        return $query->where('is_fixed', true);
    }

    public function scopeIsVariable(Builder $query): Builder
    {
        return $query->where('is_variable', true);
    }

    public function scopeIsSeasonal(Builder $query): Builder
    {
        return $query->where('is_seasonal', true);
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }

    public function getFormattedValueAttribute(): string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    public function getFormattedIsRecurredAttribute(): string
    {
        return $this->is_recurred ? 'Sim' : 'Não';
    }
}
