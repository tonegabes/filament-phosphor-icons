<?php

declare(strict_types=1);

use Filament\Support\Enums\IconSize;
use ToneGabes\Filament\Icons\Enums\Phosphor;
use ToneGabes\Filament\Icons\Enums\Weight;

require __DIR__.'/../vendor/autoload.php';

$failures = [];

function test(string $description, callable $callback): void
{
    global $failures;

    try {
        $callback();
        echo '.';
    } catch (Throwable $throwable) {
        $failures[] = [$description, $throwable];
        echo 'F';
    }
}

function assertSameValue(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf(
            'Expected %s, got %s.',
            var_export($expected, true),
            var_export($actual, true),
        ));
    }
}

test('returns prefixed icon label', function (): void {
    assertSameValue('phosphor-star', Phosphor::Star->getLabel());
});

test('returns the same icon for every Filament icon size', function (): void {
    foreach (IconSize::cases() as $size) {
        assertSameValue('phosphor-star', Phosphor::Star->getIconForSize($size));
    }
});

test('forces regular icons to weighted variants', function (): void {
    assertSameValue('phosphor-star-thin', Phosphor::Star->thin());
    assertSameValue('phosphor-star-light', Phosphor::Star->light());
    assertSameValue('phosphor-star-fill', Phosphor::Star->fill());
    assertSameValue('phosphor-star-duotone', Phosphor::Star->duotone());
    assertSameValue('phosphor-star-bold', Phosphor::Star->bold());
});

test('forces weighted icons back to regular', function (): void {
    assertSameValue('phosphor-star', Phosphor::StarBold->regular());
});

test('changes one weighted variant to another', function (): void {
    assertSameValue('phosphor-star-light', Phosphor::StarBold->forceWeight(Weight::Light));
});

test('accepts string weight values', function (): void {
    assertSameValue('phosphor-star-duotone', Phosphor::Star->forceWeight('duotone'));
});

test('keeps the original icon when the condition is false', function (): void {
    assertSameValue('phosphor-star-bold', Phosphor::StarBold->fill(false));
});

test('enum values match the installed Phosphor SVG set', function (): void {
    $svgPath = __DIR__.'/../vendor/codeat3/blade-phosphor-icons/resources/svg';

    if (! is_dir($svgPath)) {
        throw new RuntimeException("SVG directory not found: {$svgPath}");
    }

    $svgIcons = array_map(
        static fn (string $path): string => pathinfo($path, PATHINFO_FILENAME),
        glob($svgPath.'/*.svg') ?: [],
    );

    $enumIcons = array_map(
        static fn (Phosphor $icon): string => $icon->value,
        Phosphor::cases(),
    );

    sort($svgIcons);
    sort($enumIcons);

    assertSameValue($svgIcons, $enumIcons);
});

echo PHP_EOL;

if ($failures !== []) {
    foreach ($failures as [$description, $throwable]) {
        fwrite(STDERR, PHP_EOL."Failed: {$description}".PHP_EOL);
        fwrite(STDERR, $throwable->getMessage().PHP_EOL);
    }

    exit(1);
}

echo 'All tests passed.'.PHP_EOL;
