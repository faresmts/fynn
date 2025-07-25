<?php

use App\Livewire\Forms\TransactionForm;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use WithPagination;
    use Interactions;

    public TransactionForm $form;

    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $month = '';

    private bool $isReceipt = true;

    public function mount(): void
    {
        $this->month = now()->startOfMonth()->format('Y-m');
    }

    public function updated($property, $value): void
    {
        if ($property === 'month') {
            if (!$value) {
                $this->month = now()->format('Y-m');
            }

            $this->with();
        }
    }

    public function delete(string $id): void
    {
        Transaction::query()->findOrFail($id)->delete();

        $this->toast()->success('Receita deletada com sucesso!')->send();
    }

    public function with(): array
    {
        $startDate = Carbon::parse($this->month);
        $endDate = $startDate->copy()->endOfMonth()->format('Y-m-d');
        $startDate = $startDate->format('Y-m-d');

        return [
            'headers' => [
                ['index' => 'formatted_date', 'label' => 'Data'],
                ['index' => 'source', 'label' => 'Origem'],
                ['index' => 'description', 'label' => 'Descrição'],
                ['index' => 'formatted_value', 'label' => 'Valor'],
                ['index' => 'action', 'label' => 'Ações']
            ],
            'rows' => Transaction::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('source', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->isReceipt()
                ->orderBy('date')
                ->paginate($this->quantity)
                ->withQueryString(),
            'type' => 'data',
        ];
    }

    public function save(): void
    {
        $this->form->store($this->isReceipt);

        $this->toast()->success('Receita criada com sucesso!')->send();
    }

    public function edit(Transaction $receipt): void
    {
        $this->resetErrorBag();
        $this->form->reset();
        $this->form->setReceipt($receipt);
    }

    public function update(Transaction $receipt): void
    {
        $this->form->update($receipt);

        $this->toast()->success('Receita atualizada com sucesso!')->send();
    }

    public function clearForm(): void
    {
        $this->resetErrorBag();
        $this->form->reset();
    }
}; ?>

<div>
    <div class="mb-5 flex justify-between items-end">
        <div>
            <x-modal id="modal-create-receipts">
                <h2 class="mb-4">Adicionar Receita</h2>

                <form wire:submit="save">
                    <div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <x-date label="Data" format="DD/MM/YYYY" wire:model="form.date"/>
                            <x-input label="Origem" wire:model="form.source"/>
                            <x-input label="Descrição" wire:model="form.description"/>
                            <x-currency label="Valor" step="0.1" min="0" wire:model="form.value" locale="pt-BR"  symbol currency />
                        </div>

                        <x-button text="Criar" color="primary" type="submit" x-on:click="$modalClose('modal-create-receipts')"/>
                    </div>
                </form>
            </x-modal>

            <x-button color="primary" x-on:click="$modalOpen('modal-create-receipts')" wire:click="clearForm">
                <x-icon name="plus-circle" class="h-5 w-5" >
                    <x-slot:right>
                        Adicionar Receita
                    </x-slot:right>
                </x-icon>
            </x-button>
        </div>

        <div>
            <x-date label="Mês da Receita" month-year-only wire:model.live="month"/>
        </div>
    </div>
    <div>
        <x-table :$headers :$rows paginate>
            @interact('column_action', $row)
            <div class="flex gap-2" wire:key="{{ $row->id }}">
                <div>
                    <x-modal id="modal-update-receipts-{{ $row->id }}">
                        <h2 class="mb-4 font-bold text-lg">Editar Receita - {{ $row->source }}
                            ({{ \Illuminate\Support\Carbon::parse($row->date)->format('d/m/Y') }})</h2>

                        <form wire:submit="update({{ $row }})">
                            <div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <x-date label="Data" format="DD/MM/YYYY" wire:model="form.date"/>
                                    <x-input label="Origem" wire:model="form.source"/>
                                    <x-input label="Descrição" wire:model="form.description"/>
                                    <x-number label="Valor" step="0.1" min="0" wire:model="form.value"/>
                                </div>

                                <x-button text="Editar" color="amber" type="submit"/>
                            </div>
                        </form>
                    </x-modal>

                    <x-button.circle
                        color="amber"
                        icon="pencil"
                        x-on:click="$modalOpen('modal-update-receipts-{{ $row->id }}')"
                        wire:click="edit({{ $row }})"
                    />
                </div>

                <x-button.circle
                    color="red"
                    icon="trash"
                    wire:click="delete('{{ $row->id }}')"
                />
            </div>
            @endinteract
        </x-table>
    </div>
</div>
