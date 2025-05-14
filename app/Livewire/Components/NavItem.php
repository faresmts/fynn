<?php

namespace App\Livewire\Components;

use Livewire\Component;

class NavItem extends Component
{
    public string $route;
    public string $name;
    public string $icon;
    public bool $isActive = false;
    public string $classes = 'flex px-4 py-4 rounded rounded-2xl transition transition-discrete duration-150 ease-in-out text-sm ';

    public function mount(): void
    {
        $this->setupClasses();
    }

    public function render()
    {
        return view('livewire.components.nav-item');
    }

    private function setupClasses(): void
    {
        $activeClasses = $this->isActive
            ? 'bg-zinc-600 text-sm font-medium text-white dark:text-gray-100'
            : 'text-zinc-600';

        $this->classes .= $activeClasses;
    }
}
