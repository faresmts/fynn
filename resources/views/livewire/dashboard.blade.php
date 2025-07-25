<?php

use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Volt\Component;

new class extends Component {
    public array $chartData = [];

    public function mount(): void
    {
        $this->prepareChartData();
    }

    public function prepareChartData(): void
    {
        $amountReceipts = $this->monthlyAmounts();
        $amountExpenses = $this->monthlyAmounts(false);
        $monthNames = collect(range(1, 12))->map(fn($month) =>
            Carbon::create()->month($month)->translatedFormat('M')
        );

        $this->chartData = [
            'labels' => $monthNames,
            'dataReceipts' => array_values($amountReceipts),
            'dataExpenses' => array_values($amountExpenses),
        ];
    }

    public function monthlyAmounts(bool $isReceipt = true): array
    {
        $amounts = array_fill(1, 12, 0);
        $year = now()->year;

        $monthly = Transaction::query()
            ->when($isReceipt, function ($query) {
                return $query->isReceipt();
            })
            ->when(!$isReceipt, function ($query) {
                return $query->isDebt();
            })
            ->selectRaw('EXTRACT(MONTH FROM date) as month, SUM(value) as total')
            ->whereYear('date', $year)
            ->groupByRaw('EXTRACT(MONTH FROM date)')
            ->orderBy('month')
            ->pluck('total', 'month');

        foreach ($monthly as $month => $total) {
            $amounts[$month] = (float) $total;
        }

        return $amounts;
    }

    public function receipts(): array
    {
        $amounts = $this->monthlyAmounts(true);

        $currentReceipt = $amounts[date('n')];
        $lastMonthReceipt = $amounts[date('n') - 1] ?? 0;

        $currentReceiptFormatted = 'R$ ' . number_format($currentReceipt, 2, ',', '.');

        return [
            'current' => $currentReceiptFormatted,
            'value' => $currentReceipt,
            'is_increase' => $currentReceipt > $lastMonthReceipt,
        ];
    }

    public function debts(): array
    {
        $amounts = $this->monthlyAmounts(false);

        $currentDebt = $amounts[date('n')];

        $currentDebtFormatted = 'R$ ' . number_format($currentDebt, 2, ',', '.');

        return [
            'current' => $currentDebtFormatted,
            'value' => $currentDebt,
        ];
    }

    public function balance(): array
    {
        $receipts = $this->receipts()['value'];
        $debts = $this->debts()['value'];

        $balance = $receipts - $debts;
        $balanceFormatted = 'R$ ' . number_format($balance, 2, ',', '.');

        return [
            'formatted' => $balanceFormatted,
            'value' => $balance,
        ];
    }
}; ?>

<div>
    <h1 class="text-4xl mb-4 font-bold">Dashboard</h1>

    <div class="flex gap-5">
        <x-stats
            navigate href="{{ route('receipts') }}"
            title="Total de Receitas no Mês"
            icon="arrow-up"
            color="green"
            number="{{ $this->receipts()['current'] }}"
            :increase="$this->receipts()['is_increase']"
            :decrease="$this->receipts()['is_increase'] === false"
        />
        <x-stats
            navigate href="{{ route('expenses') }}"
            title="Total de Despesas no Mês"
            icon="arrow-up"
            color="red"
            number="{{ $this->debts()['current'] }}"
        />
        <x-stats
            title="Balanço do Mês"
            icon="currency-dollar"
            color="{{ $this->balance()['value'] < 0 ? 'red' : 'green' }}"
            number="{{ $this->balance()['formatted']}}"
        />
    </div>

    <div class="flex flex-col lg:flex-row gap-10 mt-10 pr-9">
        <div class="w-1/2 bg-white p-4 rounded-lg shadow-md">
            <canvas id="receiptsByMonthChart"></canvas>
        </div>

        <div class="w-1/2 bg-white p-4 rounded-lg shadow-md">
            <canvas id="debtsByMonthChart"></canvas>
        </div>
    </div>

</div>

@script
<script>
    const canvasElementReceipts = document.getElementById('receiptsByMonthChart');
    if (canvasElementReceipts) {
        const chartData = @json($chartData);

        const existingChart = Chart.getChart(canvasElementReceipts);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(canvasElementReceipts.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Receitas por Mês',
                    data: chartData.dataReceipts,
                    backgroundColor: 'rgba(49, 104, 0, 0.2)',
                    borderColor: 'rgba(49, 104, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, ticks) {
                                return new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    const canvasElementDebts = document.getElementById('debtsByMonthChart');
    if (canvasElementDebts) {
        const chartData = @json($chartData);

        const existingChart = Chart.getChart(canvasElementDebts);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(canvasElementDebts.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Despesas por Mês',
                    data: chartData.dataExpenses,
                    backgroundColor: 'rgba(255 ,92, 80, 0.2)',
                    borderColor: 'rgba(255 ,92, 80, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, ticks) {
                                return new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endscript

