<div>
    <a href="{{ route($route) }}" wire:navigate class="{{ $classes }}">
        <x-icon :name="$icon" class="h-5 w-5">
            <x-slot:right>
                {{ $name }}
            </x-slot:right>
        </x-icon>
    </a>
</div>
