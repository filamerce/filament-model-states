<?php

namespace Filamerce\FilamentModelStates\Concerns;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;
use Spatie\ModelStates\State;

/**
 * @mixin State
 */
trait HasFilamentSupport
{
    public static function label(): ?string
    {
        // @phpstan-ignore-next-line Unsafe usage of new static().
        $state = new static(null);

        if ($state instanceof HasLabel) {
            return $state->getLabel();
        }

        if (\property_exists($state, 'name')) {
            return $state::$name;
        }

        return $state::getMorphClass();
    }

    /**
     * @return Collection<string,mixed>
     */
    public static function options(): Collection
    {
        /** @var Collection<string,class-string> $states */
        $states = static::all();

        // @phpstan-ignore-next-line
        return $states->mapWithKeys(
            fn (string $state, string $name): array => [$name => $state::label()]
        );
    }
}
