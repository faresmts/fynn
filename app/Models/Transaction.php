<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
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
