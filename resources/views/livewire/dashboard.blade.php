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
        $amounts = $this->monthlyAmounts();
        $monthNames = collect(range(1, 12))->map(fn($month) =>
            Carbon::create()->month($month)->translatedFormat('M')
        );


        $this->chartData = [
            'labels' => $monthNames,
            'data' => array_values($amounts),
        ];
    }

    public function monthlyAmounts(): array
    {
        $amounts = array_fill(1, 12, 0);
        $year = now()->year;

        $monthly = Transaction::query()
            ->isReceipt()
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
        $amounts = $this->monthlyAmounts();

        $currentReceipt = $amounts[date('n')];
        $lastMonthReceipt = $amounts[date('n') - 1] ?? 0;

        $currentReceiptFormatted = 'R$ ' . number_format($currentReceipt, 2, ',', '.');

        return [
            'current' => $currentReceiptFormatted,
            'is_increase' => $currentReceipt > $lastMonthReceipt,
        ];
    }
}; ?>

<div>
    <h1 class="text-4xl mb-4 font-bold">Dashboard</h1>

    <div class="flex gap-5">
        <x-stats
            navigate href="{{ route('receipts') }}"
            title="Total em Receitas do Mês"
            icon="arrow-up"
            number="{{ $this->receipts()['current'] }}"
            :increase="$this->receipts()['is_increase']"
            :decrease="$this->receipts()['is_increase'] === false"
        />
        <x-stats color="red" title="Total em Despesas do Mês" icon="arrow-down" number="R$ 25,00" decrease/>
        <x-stats color="green" title="Balanço do Mês" icon="currency-dollar" number="R$ 25,00" decrease/>
    </div>

    <div class="flex gap-10">
        <div class="mt-5 w-full bg-white p-4 rounded-lg shadow-md">
            <canvas id="receiptsByMonthChart"></canvas>
        </div>
        <div class="mt-5 w-full bg-white p-4 rounded-lg shadow-md">
            <canvas id="receiptsByMonthChart"></canvas>
        </div>
    </div>

</div>

@script
<script>
    const canvasElement = document.getElementById('receiptsByMonthChart');
    if (canvasElement) {
        const chartData = @json($chartData);

        const existingChart = Chart.getChart(canvasElement);
        if (existingChart) {
            existingChart.destroy();
        }

        new Chart(canvasElement.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Receitas por Mês',
                    data: chartData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
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

