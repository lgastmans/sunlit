<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class InputGroup extends Component
{

    public $label;
    public $name;
    public $value;
    public $message;
    public $required;
    public $position;
    public $symbol;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $name, $value, $message, $required, $position, $symbol)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->message = $message;
        $this->required = $required;
        $this->position = $position;
        $this->symbol = $symbol;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.input-group');
    }
}
