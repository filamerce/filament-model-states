<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Override;

class ChangeStateAction extends Action
{
    use CanCustomizeProcess;

    private string $stateProperty = 'state';

    /**
     * @var class-string
     */
    private string $newState;

    /**
     * @var class-string|null
     */
    private ?string $problemState = null;

    private ?Closure $operation = null;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => __($this->getNewState()::label()));

        // $this->modalHeading(fn (): string => __('Zmienić status na :label?', ['label' => $this->getNewState()::label()]));

        // $this->modalSubmitActionLabel('??');

        // $this->successNotificationTitle(__('Status został zmieniony'));

        $this->icon('heroicon-o-bolt');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-o-bolt');

        $this->authorize(fn (Model $record) => $record->{$this->getStatePropertyName()}->canTransitionTo($this->getNewState()));

        $this->action(function (Model $record): void {
            $propertyName = $this->getStatePropertyName();
            $newState = $this->getNewState();
            $operation = $this->getOperation();

            $result = true;

            if ($operation !== null) {
                $result = $operation($record);
            }

            if ($result === true) {
                // Status can be transited in $operation(), so we need to check it again
                if (! $record->{$propertyName}->equals($newState)) {
                    $record->{$propertyName}->transitionTo($newState);
                }
            } elseif ($this->getProblemState() !== null) {
                $record->{$propertyName}->transitionTo($this->getProblemState());
            }

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

    /**
     * @param  class-string  $newState
     */
    public function problemState(string $problemState): static
    {
        $this->problemState = $problemState;

        return $this;
    }

    /**
     * @return class-string|null
     */
    public function getProblemState(): ?string
    {
        return $this->evaluate($this->problemState);
    }

    public function operation(Closure $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getOperation(): ?Closure
    {
        return $this->operation;
    }
}
