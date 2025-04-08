<?php

declare(strict_types=1);

namespace Filamerce\FilamentModelStates\Actions;

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
        $this->action(function (): void {
            $propertyName = $this->getStatePropertyName();
            $newState = $this->getNewState();

            $allRecords = 0;
            $changedRecords = 0;

            $this->process(static function (Collection $records) use ($propertyName, $newState, &$allRecords, &$changedRecords): void {
                $allRecords = $records->count();

                $records->each(static function ($record) use ($propertyName, $newState, &$changedRecords): void {
                    assert($record instanceof Model);

                    if (! $record->{$propertyName}->canTransitionTo($newState)) {
                        return;
                    }

                    $changedRecords++;

                    $record->{$propertyName}->transitionTo($newState);
                });
            });

            if ($changedRecords === 0) {
                $this->failure();

                Notification::make()->warning()->title(__('filament-model-states::translations.bulk_fail'))->send();

                return;
            }

            $this->success();

            Notification::make()->success()->title(__('filament-model-states::translations.bulk_success', [
                'changed_count' => $changedRecords,
                'all_count' => $allRecords,
            ]))->send();
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
