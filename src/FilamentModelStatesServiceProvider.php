<?php

namespace Filamerce\FilamentModelStates;

use Filamerce\FilamentModelStates\Testing\TestsFilamentModelStates;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentModelStatesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-model-states';

    public static string $viewNamespace = 'filament-model-states';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations();

    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Testing
        Testable::mixin(new TestsFilamentModelStates);
    }
}
