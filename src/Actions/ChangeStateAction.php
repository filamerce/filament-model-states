<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Override;

class ChangeStateAction extends Action
{
    use CanCustomizeProcess;

    private string $property = 'state';

    private string $newState;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => __($this->getNewState()::label()));

        $this->modalHeading(fn (): string => __('Zmienić status na :label?', ['label' => $this->getNewState()::label()]));

        $this->modalSubmitActionLabel('??');

        $this->successNotificationTitle(__('Status został zmieniony'));

        $this->icon('phosphor-path');

        $this->requiresConfirmation();

        $this->modalIcon('phosphor-path');

        $this->visible(fn (Model $record) => $record->{$this->getPropertyName()}->canTransitionTo($this->getNewState()));

        $this->action(function (): void {
            $propertyName = $this->getPropertyName();
            $newState = $this->getNewState();
            $result = $this->process(static fn (Model $record) => $record->{$propertyName}->transitionTo($newState));

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'change-state';
    }

    public function property(string $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getPropertyName(): string
    {
        return $this->property;
    }

    public function newState(string $newState): static
    {
        $this->newState = $newState;

        return $this;
    }

    public function getNewState(): string
    {
        return $this->newState;
    }
}
