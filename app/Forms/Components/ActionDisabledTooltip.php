<?php

namespace App\Forms\Components;

use Closure;
use Filament\Actions\Action;

class ActionDisabledTooltip extends Action
{
    protected Action $child;
    protected string $view = 'forms.components.action-disabled-tooltip';
    protected Closure|bool $isPolling = true;

    public function child(Action $action): ActionDisabledTooltip
    {
        $this->child = $action;
        return $this;
    }
    public function getAction(): Action
    {
        return $this->child;
    }

    public function isPolling(Closure|bool $isPolling): ActionDisabledTooltip
    {
        $this->isPolling = $isPolling;
        return $this;
    }

    public function getIsPolling(): bool
    {
        return $this->evaluate($this->isPolling);
    }
}
