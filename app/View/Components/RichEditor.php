<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RichEditor extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public ?string $value = '',
        public string $placeholder = 'Enter description...',
        public string $minHeight = '160px',
        public int $rows = 5,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.rich-editor');
    }
}

