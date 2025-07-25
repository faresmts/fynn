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

    private bool $isReceipt = false;

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

        $this->toast()->success('Despesa deletada com sucesso!')->send();
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
                ->isDebt()
                ->orderBy('date')
                ->paginate($this->quantity)
                ->withQueryString(),
            'type' => 'data',
        ];
    }

    public function save(): void
    {
        $this->form->store($this->isReceipt);

        $this->toast()->success('Despesa criada com sucesso!')->send();
    }

    public function edit(Transaction $receipt): void
    {
        $this->resetErrorBag();
        $this->form->reset();
        $this->form->setTransaction($receipt);
    }

    public function update(Transaction $receipt): void
    {
        $this->form->update($receipt);

        $this->toast()->success('Despesa atualizada com sucesso!')->send();
    }

    public function clearForm(): void
    {
        $this->resetErrorBag();
        $this->form->reset();
    }

    public function receipts(): array
    {
        $selectedDate = Carbon::parse($this->month)->startOfMonth();

        $previousMonthDate = $selectedDate->copy()->subMonthNoOverflow();

        $currentDebt = Transaction::query()
            ->isDebt()
            ->whereYear('date', $selectedDate->year)
            ->whereMonth('date', $selectedDate->month)
            ->sum('value');

        $lastMonthDebt = Transaction::query()
            ->isDebt()
            ->whereYear('date', $previousMonthDate->year)
            ->whereMonth('date', $previousMonthDate->month)
            ->sum('value');

        $currentDebtFormatted = 'R$ ' . number_format($currentDebt, 2, ',', '.');

        return [
            'current' => $currentDebtFormatted,
            'is_increase' => $currentDebt > $lastMonthDebt,
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl  font-bold">Despesas</h1>
        <div class="w-80">
            <x-stats
                title="Total em Despesas do Mês"
                icon="arrow-down"
                color="red"
                number="{{$this->receipts()['current']}}"
                :increase="$this->receipts()['is_increase']"
                :decrease="$this->receipts()['is_increase'] === false"
            />
        </div>
    </div>

    <div class="mb-5 flex justify-between items-end">
        <div>
            <x-modal id="modal-create-receipts">
                <h2 class="mb-4">Adicionar Despesa</h2>

                <form wire:submit="save">
                    <div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <x-date label="Data" format="DD/MM/YYYY" wire:model="form.date"/>
                            <x-input label="Origem" wire:model="form.source"/>
                            <x-input label="Descrição" wire:model="form.description"/>
                            <x-currency label="Valor" step="0.1" min="0" wire:model="form.value" locale="pt-BR" symbol
                                        currency/>
                        </div>

                        <x-button text="Criar" color="red" type="submit"
                                  x-on:click="$modalClose('modal-create-receipts')"/>
                    </div>
                </form>
            </x-modal>

            <x-button color="red" x-on:click="$modalOpen('modal-create-receipts')" wire:click="clearForm">
                <x-icon name="plus-circle" class="h-5 w-5">
                    <x-slot:right>
                        Adicionar Despesa
                    </x-slot:right>
                </x-icon>
            </x-button>
        </div>

        <div>
            <x-date label="Mês da Despesa" month-year-only wire:model.live="month"/>
        </div>
    </div>
    <div>
        <x-table :$headers :$rows paginate>
            @interact('column_action', $row)
            <div class="flex gap-2" wire:key="{{ $row->id }}">
                <div>
                    <x-modal id="modal-update-receipts-{{ $row->id }}">
                        <h2 class="mb-4 font-bold text-lg">Editar Despesa - {{ $row->source }}
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
