<?php

declare(strict_types=1);

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filamerce\FilamentModelStates\Actions\ChangeStateAction;
use Filamerce\FilamentModelStates\View\ModelStatesIconAlias;

function changeStateAction(): ChangeStateAction
{
    return ChangeStateAction::make('change')->newState('App\\States\\Done');
}

it('uses the bolt Heroicon by default', function (): void {
    $action = changeStateAction();

    expect($action->getIcon())->toBe(Heroicon::OutlinedBolt)
        ->and($action->getModalIcon())->toBe(Heroicon::OutlinedBolt);
});

it('lets the host replace the icon through its alias, and the modal follows it', function (): void {
    FilamentIcon::register([ModelStatesIconAlias::ACTIONS_CHANGE_STATE => Heroicon::Star]);

    $action = changeStateAction();

    expect($action->getIcon())->toBe(Heroicon::Star)
        ->and($action->getModalIcon())->toBe(Heroicon::Star);
});

it('lets the host give the modal its own icon', function (): void {
    FilamentIcon::register([
        ModelStatesIconAlias::ACTIONS_CHANGE_STATE => Heroicon::Star,
        ModelStatesIconAlias::ACTIONS_CHANGE_STATE_MODAL => Heroicon::Heart,
    ]);

    $action = changeStateAction();

    expect($action->getIcon())->toBe(Heroicon::Star)
        ->and($action->getModalIcon())->toBe(Heroicon::Heart);
});

it('keeps a per-instance icon above a registered alias', function (): void {
    FilamentIcon::register([ModelStatesIconAlias::ACTIONS_CHANGE_STATE => Heroicon::Star]);

    expect(changeStateAction()->icon(Heroicon::Heart)->getIcon())->toBe(Heroicon::Heart);
});
