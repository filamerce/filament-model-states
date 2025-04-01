<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Columns;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Tables\Columns\TextColumn;

class StateColumn extends TextColumn
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
            ->label(__('State'))
            ->toggleable()
            ->sortable();
    }
}
