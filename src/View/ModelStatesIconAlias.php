<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\View;

/**
 * Icon aliases this package resolves, so a host can re-icon state transitions globally. Register them
 * exactly as Filament's own:
 *
 * ```php
 * use Filament\Support\Facades\FilamentIcon;
 * use Filamerce\FilamentModelStates\View\ModelStatesIconAlias;
 *
 * FilamentIcon::register([
 *     ModelStatesIconAlias::ACTIONS_CHANGE_STATE => Phosphor::Lightning,
 * ]);
 * ```
 *
 * The string values are part of the public contract — a host registers against them — so they may
 * not be renamed without a breaking release. They share the package's name, which is what keeps them
 * from colliding with another package's aliases.
 *
 * Per-instance modifiers (`->icon()`, `->modalIcon()`) still win over a registered alias: the alias is
 * the default, the modifier is the exception.
 */
class ModelStatesIconAlias
{
    /** The button that moves a record to another state. Default: Heroicon::OutlinedBolt. */
    const ACTIONS_CHANGE_STATE = 'filament-model-states::actions.change-state';

    /**
     * The icon of the confirmation modal of that button.
     *
     * Falls back to ACTIONS_CHANGE_STATE, so a host re-icons both with one registration; register this
     * one only to give the modal a different icon. Default: Heroicon::OutlinedBolt.
     */
    const ACTIONS_CHANGE_STATE_MODAL = 'filament-model-states::actions.change-state-modal';
}
