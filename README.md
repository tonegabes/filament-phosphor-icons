# Filament Phosphor Icons

A Phosphor icon set ready to be used in a Filament 4 application.

## Installation

You can install the package via composer:

```bash
composer require tonegabes/filament-phosphor-icons
```

## Usage

After the package is installed, you can use it anywhere in  your application as an Enum:
All icons are available through an enum providing convenient usage throughout Filament. For more information, check the [Filament docs](https://filamentphp.com/docs/4.x/styling/icons).

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use ToneGabes\Filament\Icons\Enums\Phosphor;

Action::make('star')->icon(Phosphor::StarBold);

Toggle::make('is_starred')
  ->onIcon(Phosphor::Star)

```

## Variant helpers

You can quickly switch icon weights using helper methods on the enum value:

- thin
- light
- regular
- bold
- fill
- duotone

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use ToneGabes\Filament\Icons\Enums\Phosphor;

Action::make('star')->icon(Phosphor::Star->bold());

Toggle::make('is_starred')
    ->onIcon(Phosphor::Star->thin())
    ->offIcon(Phosphor::Star->regular());
```

Important: helper methods override the enum case's default style. For example, even if you pick a bold case, calling a helper will switch the weight:

```php
// Overrides the default bold case to thin at runtime
Action::make('star')->icon(Phosphor::StarBold->thin());
```

```php
Action::make('star')->icon(Phosphor::Star->forceWeight($var));

```

If you would like to use an icon in a Blade component, you can pass it as an attribute:

```php
@php
    use ToneGabes\Filament\Icons\Enums\Phosphor;
@endphp

<x-filament::badge :icon="Phosphor::Star">
    Star
</x-filament::badge>

```

## Credits

- [Tone Gabes](https://github.com/tonegabes)
- [Phosphor icons](https://phosphoricons.com)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License](LICENSE.md) for more information.
