<?php

namespace JeffersonGoncalves\FilamentCheckWhoisWidget\Widgets;

use AllowDynamicProperties;
use AshAllenDesign\FaviconFetcher\Facades\Favicon;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use IP2WHOIS\Api;
use IP2WHOIS\Configuration;

#[AllowDynamicProperties]
class CheckWhoisWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament-check-whois-widget::filament.widgets.check-whois-widget';

    protected array $domainWhois = [];

    public function __construct()
    {
        $domains = [];

        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        if ($plugin->getDomains()) {
            $domains = $plugin->getDomains();
        }

        $config = new Configuration(config('filament-check-whois-widget.ip2_whois_api_key'));
        $ip2whois = new Api($config);

        foreach ($domains as $domain) {
            $this->domainWhois[] = Cache::remember("filament-check-whois-widget-$domain", 2592000, fn () => $this->getWhois($ip2whois, $domain));
        }
    }

    private function getWhois(Api $ip2whois, string $domain): array
    {
        $whois = $ip2whois->lookup($domain);
        if (property_exists($whois, 'error')) {
            return [
                'domain' => $domain,
                'is_valid' => false,
                'favicon' => null,
            ];
        }

        return [
            'domain' => $domain,
            'is_valid' => true,
            'create_date' => property_exists($whois, 'create_date') ? $this->parseDate($whois->create_date) : null,
            'update_date' => property_exists($whois, 'update_date') ? $this->parseDate($whois->update_date) : null,
            'expire_date' => property_exists($whois, 'expire_date') ? $this->parseDate($whois->expire_date) : null,
            'domain_age' => property_exists($whois, 'domain_age') ? $whois->domain_age : null,
            'favicon' => $this->getFaviconByDomain($domain),
        ];
    }

    private function parseDate(string $date): false | Carbon
    {
        if (strlen($date) === 8) {
            return Carbon::createFromFormat('Ymd', $date);
        }

        return Carbon::createFromTimeString($date);
    }

    private function getFaviconByDomain(string $domain): ?string
    {
        if (! Str::contains($domain, ['http://', 'https://'])) {
            $domain = 'https://' . $domain;
        }

        try {
            return Favicon::fetch($domain)?->cache(now()->addDay())->getFaviconUrl() ?? null;
        } catch (Exception $ignored) {
            return null;
        }
    }

    public static function getSort(): int
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getSort() ?? -1;
    }

    public function getColumnSpan(): int | string | array
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getColumnSpan();
    }

    public function render(): View
    {
        return view(static::$view, [
            'domainWhois' => $this->domainWhois,
            'shouldShowTitle' => $this->shouldShowTitle(),
            'title' => $this->title(),
            'description' => $this->description(),
            'quantityPerRow' => $this->quantityPerRow() ?? '1',
        ]);
    }

    public function shouldShowTitle(): bool
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getShouldShowTitle();
    }

    public function title(): ?string
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getTitle();
    }

    public function description(): ?string
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getDescription();
    }

    public function quantityPerRow(): ?int
    {
        /** @var \JeffersonGoncalves\FilamentCheckWhoisWidget\FilamentCheckWhoisWidgetPlugin $plugin */
        $plugin = Filament::getCurrentPanel()?->getPlugin('filament-check-whois-widget');

        return $plugin->getQuantityPerRow();
    }
}
