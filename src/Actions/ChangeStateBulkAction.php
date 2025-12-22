<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Override;

class ChangeStateBulkAction extends BulkAction
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

        $this->icon('phosphor-path');

        $this->requiresConfirmation();

        $this->modalIcon('phosphor-path');
        $this->action(function (Collection $records): void {

            $allRecords = $records->count();
            $changedRecords = 0;

            $records->each(function ($record) use (&$changedRecords): void {
                assert($record instanceof Model);

                $propertyName = $this->getStatePropertyName();
                $newState = $this->getNewState();
                $operation = $this->getOperation();
                $problemState = $this->getProblemState();

                $result = true;

                if ($operation !== null) {
                    $result = $operation($record);
                }

                if ($result === true) {
                    // Check if transition is allowed and state is different
                    if (! $record->{$propertyName}->equals($newState) && $record->{$propertyName}->canTransitionTo($newState)) {
                        $record->{$propertyName}->transitionTo($newState);
                        $changedRecords++;
                    } elseif ($record->{$propertyName}->equals($newState)) {
                        // Already in target state, count as changed
                        $changedRecords++;
                    }
                    // If transition is not allowed, skip this record silently
                } elseif ($this->getProblemState() !== null) {
                    $record->{$propertyName}->transitionTo($this->getProblemState());
                }

            });

            if ($changedRecords === 0) {
                Notification::make()->danger()->title(__('filament-model-states::translations.bulk_fail'))->send();

                return;
            }

            if ($changedRecords < $allRecords) {
                Notification::make()->warning()->title(__('filament-model-states::translations.bulk_partial_fail', [
                    'changed_count' => $changedRecords,
                    'all_count' => $allRecords,
                ]))->send();

                return;
            }

            Notification::make()->success()->title(__('filament-model-states::translations.bulk_success', [
                'changed_count' => $changedRecords,
                'all_count' => $allRecords,
            ]))->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'bulk-change-state';
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
