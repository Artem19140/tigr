<?php

namespace App\View\Components\pdf\components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SignatureSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $fio,
        public string $date
    )
    {
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('pdf.components.signature-section');
    }
}
