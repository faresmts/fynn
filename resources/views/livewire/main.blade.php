<?php

use Livewire\Volt\Component;

new class extends Component {

} ?>

<div>
    <x-layout>
        <x-slot:header>
            <x-layout.header>
                <x-slot:right>
                    <x-dropdown text="Hello, {{ auth()->user()->name }}!">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown.items text="Logout" onclick="event.preventDefault(); this.closest('form').submit();" />
                        </form>
                    </x-dropdown>
                </x-slot:right>
            </x-layout.header>
        </x-slot:header>

        <x-slot:menu>
            <x-side-bar>
                <a href="{{ route('home') }}" class="mb-8">
                    <x-logo class="w-auto h-12 mx-auto text-green-900" color="#316800"/>
                </a>
                <x-side-bar.item wire:navigate text="Dashboard" :current="request()->routeIs('home')" icon="chart-bar" :route="route('home')" />
                <x-side-bar.item wire:navigate text="Receitas" :current="request()->routeIs('receipts')" icon="arrow-up-circle" :route="route('receipts')" />
                <x-side-bar.item wire:navigate text="Despesas" :current="request()->routeIs('home')" icon="arrow-down-circle" :route="route('home')" />
            </x-side-bar>
        </x-slot:menu>

        @switch(true)
            @case(request()->routeIs('home'))
                <livewire:dashboard />
                @break

            @case(request()->routeIs('receipts'))
                <livewire:receipts />
                @break

            @default
                <div class="p-4">
                    <h1 class="text-2xl font-bold">Page not found</h1>
                </div>
        @endswitch

    </x-layout>
</div>
