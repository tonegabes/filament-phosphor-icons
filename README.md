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

Action::make('star')
  ->icon(Phosphor::StarBold)

Toggle::make('is_starred')
  ->onIcon(Phosphor::Star)

```

If you would like to use an icon in a Blade component, you can pass it as an attribute:

```php
@php
    use ToneGabes\Filament\Icons\Phosphor;
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
