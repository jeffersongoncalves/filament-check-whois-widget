## Filament Check Whois Widget

A Filament dashboard widget that displays WHOIS information for configured domains, including registration dates, expiry dates, domain age, and favicon. Uses the IP2WHOIS API with built-in caching. Requires Filament 5.0+.

### Installation

@verbatim
<code-snippet name="Install the plugin" lang="bash">
composer require jeffersongoncalves/filament-check-whois-widget
</code-snippet>
@endverbatim

### Publish Config

@verbatim
<code-snippet name="Publish config file" lang="bash">
php artisan vendor:publish --tag="filament-check-whois-widget-config"
</code-snippet>
@endverbatim

### Environment Variable

@verbatim
<code-snippet name="Set API key in .env" lang="env">
CHECK_WHOIS_API_KEY=your-ip2whois-api-key
</code-snippet>
@endverbatim

### Register Plugin

@verbatim
<code-snippet name="Register in PanelProvider" lang="php">
use JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentCheckWhoisWidgetPlugin::make()
                ->domains([
                    'filamentphp.com',
                    'laravel.com',
                ]),
        ]);
}
</code-snippet>
@endverbatim

### Advanced Configuration

@verbatim
<code-snippet name="Full plugin options" lang="php">
use JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin;

FilamentCheckWhoisWidgetPlugin::make()
    ->domains(['filamentphp.com', 'laravel.com'])
    ->shouldShowTitle(true)
    ->setTitle('Domain WHOIS')
    ->setDescription('Domain registration details')
    ->setQuantityPerRow(2)
    ->setColumnSpan('full')
    ->setSort(10)
</code-snippet>
@endverbatim

### Key Methods

- `domains(array $domains)` - List of domains to check
- `shouldShowTitle(bool $value)` - Show/hide widget title (default: `true`)
- `setTitle(string $value)` - Custom widget title
- `setDescription(string $value)` - Custom widget description
- `setQuantityPerRow(int $value)` - Domains per row (default: `1`)
- `setColumnSpan(int|string|array $value)` - Widget column span (default: `'1/2'`)
- `setSort(int $value)` - Widget sort order

### Architecture

- **Namespace**: `JeffersonGoncalves\FilamentCheckWhoisWidget`
- **Plugin**: `FilamentCheckWhoisWidgetPlugin` implements `Filament\Contracts\Plugin`
- **Widget**: `CheckWhoisWidget` extends `Filament\Widgets\Widget`
- **Config**: `filament-check-whois-widget.php` with `ip2_whois_api_key`
- **Caching**: Results cached for 30 days via `Cache::remember()`

### Best Practices

- Obtain a free or paid API key from IP2WHOIS before using the widget
- Use `setColumnSpan('full')` with `setQuantityPerRow(2)` or more for multiple domains
- WHOIS data is cached for 30 days per domain -- clear cache to force a refresh
- The widget is non-lazy (`$isLazy = false`) so data loads immediately with the page
