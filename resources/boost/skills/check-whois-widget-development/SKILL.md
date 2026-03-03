---
name: check-whois-widget-development
description: Build and work with the Filament Check Whois Widget plugin, including domain WHOIS lookup, IP2WHOIS API integration, caching, widget layout configuration, and favicon fetching.
---

# Check Whois Widget Development

## When to use this skill

Use this skill when:
- Adding a WHOIS domain checker widget to a Filament dashboard
- Configuring domain monitoring with registration/expiry date display
- Customizing widget layout (column span, quantity per row, sort order)
- Setting up IP2WHOIS API integration
- Troubleshooting API key, caching, or favicon issues

## Architecture

### Namespace
```
JeffersonGoncalves\FilamentCheckWhoisWidget
```

### Key Classes

| Class | Path | Description |
|-------|------|-------------|
| `FilamentCheckWhoisWidgetPlugin` | `src/FilamentCheckWhoisWidgetPlugin.php` | Plugin class implementing `Filament\Contracts\Plugin` |
| `CheckWhoisWidget` | `src/Widgets/CheckWhoisWidget.php` | Dashboard widget extending `Filament\Widgets\Widget` |
| `FilamentCheckWhoisWidgetServiceProvider` | `src/FilamentCheckWhoisWidgetServiceProvider.php` | Service provider for config, views, translations |

### Dependencies
- `filament/filament: ^5.0`
- `ip2whois/ip2whois-php: ^2.2` (WHOIS API client)
- `ashallendesign/favicon-fetcher: ^3.5` (domain favicon retrieval)
- `spatie/laravel-package-tools: ^1.15.0`

## Configuration

### Config File

```php
// config/filament-check-whois-widget.php
return [
    'ip2_whois_api_key' => env('CHECK_WHOIS_API_KEY'),
];
```

### Environment Setup

```env
CHECK_WHOIS_API_KEY=your-ip2whois-api-key
```

Get an API key from [IP2WHOIS](https://www.ip2whois.com/).

## Features

### Basic Widget Registration

```php
use JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentCheckWhoisWidgetPlugin::make()
                ->domains([
                    'filamentphp.com',
                ]),
        ]);
}
```

### Multiple Domains with Layout

```php
FilamentCheckWhoisWidgetPlugin::make()
    ->domains([
        'filamentphp.com',
        'laravel.com',
        'livewire.laravel.com',
    ])
    ->setQuantityPerRow(3)
    ->setColumnSpan('full')
    ->setSort(5)
```

### Title and Description

```php
FilamentCheckWhoisWidgetPlugin::make()
    ->domains(['example.com'])
    ->shouldShowTitle(true)           // default: true
    ->setTitle('Domain Monitor')
    ->setDescription('WHOIS registration details')
```

### Hide Widget Title

```php
FilamentCheckWhoisWidgetPlugin::make()
    ->domains(['example.com'])
    ->shouldShowTitle(false)
```

## Plugin API Reference

| Method | Type | Default | Description |
|--------|------|---------|-------------|
| `domains(array $domains)` | `array` | `[]` | Domains to query |
| `shouldShowTitle(Closure\|bool $value)` | `bool` | `true` | Show/hide widget title |
| `setTitle(Closure\|string $value)` | `string` | `''` | Widget title text |
| `setDescription(Closure\|string $value)` | `string` | `''` | Widget description text |
| `setSort(Closure\|int\|null $value)` | `int\|null` | `null` | Widget sort order (default fallback: `-1`) |
| `setColumnSpan(int\|string\|array $value)` | `mixed` | `'1/2'` | Widget column span |
| `setQuantityPerRow(Closure\|int $value)` | `int` | `1` | Domains displayed per row |

## Internal Behavior

### WHOIS Data Fetching

The `CheckWhoisWidget` constructor:
1. Retrieves configured domains from the plugin instance via `Filament::getCurrentPanel()->getPlugin('filament-check-whois-widget')`
2. Creates an `IP2WHOIS\Api` client with the configured API key
3. For each domain, calls `Cache::remember()` with a 30-day TTL (`2592000` seconds)
4. Each cached entry includes: `domain`, `is_valid`, `create_date`, `update_date`, `expire_date`, `domain_age`, `favicon`

### WHOIS Response Structure

```php
[
    'domain'      => 'example.com',
    'is_valid'    => true,
    'create_date' => Carbon::instance,  // Registration date
    'update_date' => Carbon::instance,  // Last update date
    'expire_date' => Carbon::instance,  // Expiry date
    'domain_age'  => 365,               // Age in days
    'favicon'     => 'https://...',     // Favicon URL or null
]
```

### Favicon Fetching

Uses `AshAllenDesign\FaviconFetcher\Facades\Favicon` to fetch domain favicons:
- Automatically prepends `https://` if missing
- Caches favicon URL for 1 day
- Returns `null` on failure (exception caught silently)

### Caching Strategy

- Cache key: `filament-check-whois-widget-{domain}`
- TTL: 30 days (2,592,000 seconds)
- Uses Laravel's default cache driver
- To force refresh: `Cache::forget("filament-check-whois-widget-example.com")`

### Widget Properties

- `$isLazy = false` -- Widget loads data immediately, not lazily
- Reads all configuration from the plugin singleton via `Filament::getCurrentPanel()->getPlugin()`

## Troubleshooting

### Widget Shows No Data
**Cause**: Missing or invalid API key.
**Solution**: Set `CHECK_WHOIS_API_KEY` in `.env`. Verify the key at IP2WHOIS dashboard.

### Domain Shows as Invalid
**Cause**: WHOIS API returned an error for that domain.
**Solution**: Verify the domain name is correct and publicly registered. Some TLDs may not be supported by IP2WHOIS.

### Stale WHOIS Data
**Cause**: Data is cached for 30 days.
**Solution**: Clear the specific cache entry:
```php
Cache::forget('filament-check-whois-widget-example.com');
```

### Favicon Not Displaying
**Cause**: Domain has no discoverable favicon, or the favicon fetcher timed out.
**Solution**: This is handled gracefully -- the widget shows `null` for missing favicons. No action required.

### Widget Not Appearing on Dashboard
**Cause**: Plugin not registered in the panel provider.
**Solution**: Ensure `FilamentCheckWhoisWidgetPlugin::make()->domains([...])` is added to the `->plugins([])` array in your `PanelProvider`.
