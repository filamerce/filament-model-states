<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Override;

class ChangeStateTableAction extends Action
{
    use CanCustomizeProcess;

    private string $stateProperty = 'state';

    /**
     * @var class-string
     */
    private string $newState;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => __($this->getNewState()::label()));

        // $this->modalHeading(fn (): string => __('Zmienić status na :label?', ['label' => $this->getNewState()::label()]));

        // $this->modalSubmitActionLabel('??');

        // $this->successNotificationTitle(__('Status został zmieniony'));

        $this->icon('phosphor-path');

        $this->requiresConfirmation();

        $this->modalIcon('phosphor-path');

        $this->authorize(fn (Model $record) => $record->{$this->getStatePropertyName()}->canTransitionTo($this->getNewState()));

        $this->action(function (): void {
            $propertyName = $this->getStatePropertyName();
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

    public function stateProperty(string $stateProperty): static
    {
        $this->stateProperty = $stateProperty;

        return $this;
    }

    public function getStatePropertyName(): string
    {
        return $this->stateProperty;
    }

    /**
     * @param  class-string  $newState
     */
    public function newState(string $newState): static
    {
        $this->newState = $newState;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getNewState(): string
    {
        return $this->newState;
    }
}
