<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Entries;

use Filament\Infolists\Components\TextEntry;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

class StateEntry extends TextEntry
{
    protected function setUp(): void
    {
        $this
            ->badge()
            ->formatStateUsing(function ($record) {
                $state = $record->{$this->name};

                if ($state instanceof HasLabel) {
                    return $state->getLabel();
                }

                return $record->{$this->name};
            })
            ->color(function ($record): string | array | null {
                $state = $record->{$this->name};

                if ($state instanceof HasColor) {
                    return $state->getColor();
                }

                return null;
            })
            ->icon(function ($record): ?string {
                $state = $record->{$this->name};

                if ($state instanceof HasIcon) {
                    return $state->getIcon();
                }

                return null;
            })
            ->label(__('filament-model-states::translations.state'));
    }
}
