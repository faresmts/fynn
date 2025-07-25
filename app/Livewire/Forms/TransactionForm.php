<?php

namespace App\Livewire\Forms;

use App\Models\Transaction;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TransactionForm extends Form
{
    #[Validate('required|string')]
    public string $source = '';

    #[Validate('required|date_format:Y-m-d')]
    public string $date = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $value = 0;

    public bool $is_receipt = true;

    /**
     * @throws ValidationException
     */
    public function store(bool $isReceipt = false):void
    {
        $this->validate();

        $data = $this->all();
        $value = $data['value'] ?? 0;
        $data['value'] = $value / 100;

        Transaction::query()->create(
            array_merge(
                $data,
                [
                    'is_receipt' => $isReceipt,
                    'is_debt' => !$isReceipt,
                    'user_id' => auth()->id()
                ]
            )
        );

        $this->reset();
    }

    /**
     * @throws ValidationException
     */
    public function update(Transaction $receipt): void
    {
        $this->validate();

        $receipt->update($this->all());

        $this->reset();
    }

    public function setTransaction(Transaction $receipt): void
    {
        $this->source = $receipt->source;
        $this->date = $receipt->date;
        $this->description = $receipt->description;
        $this->value = $receipt->value;
    }
}
