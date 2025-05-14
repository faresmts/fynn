<nav x-data="{ open: false }" class="bg-zinc-400 rounded rounded-2xl mb-10 fixed bottom-0 left-0 right-0 mx-auto w-1/2">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="ms-10 flex items-center gap-1">
                    <livewire:components.nav-item
                        route="dashboard"
                        name="{{ __('Dashboard') }}"
                        icon="presentation-chart-line"
                        isActive="{{ request()->routeIs('dashboard') }}"
                    />

                    <livewire:components.nav-item
                        route="receipts"
                        name="Receitas"
                        icon="arrow-trending-up"
                        isActive="{{ request()->routeIs('receipts') }}"
                    />

{{--                    <livewire:components.nav-item--}}
{{--                        route="debts"--}}
{{--                        name="Dívidas"--}}
{{--                        icon="home-modern"--}}
{{--                        isActive="{{ request()->routeIs('expenses') }}"--}}
{{--                    />--}}

                    <livewire:components.nav-item
                        route="expenses"
                        name="Despesas"
                        icon="arrow-trending-down"
                        isActive="{{ request()->routeIs('expenses') }}"
                    />
                </div>
            </div>
        </div>
    </div>

</nav>
