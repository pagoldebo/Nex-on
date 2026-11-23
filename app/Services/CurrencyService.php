<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

/**
 * Class CurrencyService
 *
 * Centralized service managing multi-currency exchange rates, patron currency preference,
 * precision rounding, and formatting across Project Nex-on.
 */
class CurrencyService
{
    /**
     * Complete catalogue of system-supported currencies with ISO metadata.
     *
     * @var array<string, array{code: string, symbol: string, name: string, country: string, flag: string, decimals: int, default_rate: float, locale: string}>
     */
    public const SUPPORTED_CURRENCIES = [
        'INR' => [
            'code'         => 'INR',
            'symbol'       => '₹',
            'name'         => 'Indian Rupee',
            'country'      => 'India',
            'flag'         => '🇮🇳',
            'decimals'     => 0,
            'default_rate' => 1.0,
            'locale'       => 'en-IN',
        ],
        'USD' => [
            'code'         => 'USD',
            'symbol'       => '$',
            'name'         => 'US Dollar',
            'country'      => 'United States',
            'flag'         => '🇺🇸',
            'decimals'     => 2,
            'default_rate' => 0.0116,
            'locale'       => 'en-US',
        ],
        'EUR' => [
            'code'         => 'EUR',
            'symbol'       => '€',
            'name'         => 'Euro',
            'country'      => 'European Union',
            'flag'         => '🇪🇺',
            'decimals'     => 2,
            'default_rate' => 0.0108,
            'locale'       => 'de-DE',
        ],
        'GBP' => [
            'code'         => 'GBP',
            'symbol'       => '£',
            'name'         => 'British Pound',
            'country'      => 'United Kingdom',
            'flag'         => '🇬🇧',
            'decimals'     => 2,
            'default_rate' => 0.0092,
            'locale'       => 'en-GB',
        ],
        'AED' => [
            'code'         => 'AED',
            'symbol'       => 'AED ',
            'name'         => 'UAE Dirham',
            'country'      => 'United Arab Emirates',
            'flag'         => '🇦🇪',
            'decimals'     => 2,
            'default_rate' => 0.0426,
            'locale'       => 'ar-AE',
        ],
        'CAD' => [
            'code'         => 'CAD',
            'symbol'       => 'CA$',
            'name'         => 'Canadian Dollar',
            'country'      => 'Canada',
            'flag'         => '🇨🇦',
            'decimals'     => 2,
            'default_rate' => 0.0160,
            'locale'       => 'en-CA',
        ],
        'AUD' => [
            'code'         => 'AUD',
            'symbol'       => 'AU$',
            'name'         => 'Australian Dollar',
            'country'      => 'Australia',
            'flag'         => '🇦🇺',
            'decimals'     => 2,
            'default_rate' => 0.0178,
            'locale'       => 'en-AU',
        ],
        'SGD' => [
            'code'         => 'SGD',
            'symbol'       => 'SG$',
            'name'         => 'Singapore Dollar',
            'country'      => 'Singapore',
            'flag'         => '🇸🇬',
            'decimals'     => 2,
            'default_rate' => 0.0156,
            'locale'       => 'en-SG',
        ],
        'SAR' => [
            'code'         => 'SAR',
            'symbol'       => 'SAR ',
            'name'         => 'Saudi Riyal',
            'country'      => 'Saudi Arabia',
            'flag'         => '🇸🇦',
            'decimals'     => 2,
            'default_rate' => 0.0435,
            'locale'       => 'ar-SA',
        ],
        'JPY' => [
            'code'         => 'JPY',
            'symbol'       => '¥',
            'name'         => 'Japanese Yen',
            'country'      => 'Japan',
            'flag'         => '🇯🇵',
            'decimals'     => 0,
            'default_rate' => 1.78,
            'locale'       => 'ja-JP',
        ],
    ];

    /**
     * Determine if multi-currency conversion is enabled by store administrator.
     */
    public function isMultiCurrencyEnabled(): bool
    {
        return Setting::get('multi_currency_enabled', '1') === '1';
    }

    /**
     * Get store's base currency code (default: INR).
     */
    public function getBaseCurrency(): string
    {
        $base = strtoupper((string) Setting::get('base_currency', 'INR'));
        return array_key_exists($base, self::SUPPORTED_CURRENCIES) ? $base : 'INR';
    }

    /**
     * Return all supported currencies catalogue.
     */
    public function getAllSupported(): array
    {
        return self::SUPPORTED_CURRENCIES;
    }

    /**
     * Retrieve all active enabled currencies with their effective rates.
     *
     * @return array<string, array>
     */
    public function getEnabledCurrencies(): array
    {
        $base = $this->getBaseCurrency();
        if (! $this->isMultiCurrencyEnabled()) {
            return [$base => $this->getCurrencyMeta($base)];
        }

        $raw = Setting::get('enabled_currencies');
        $codes = [];
        if (!empty($raw)) {
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
            if (is_array($decoded)) {
                $codes = array_map('strtoupper', $decoded);
            }
        }

        // Default set of popular enabled currencies if unset
        if (empty($codes)) {
            $codes = ['INR', 'USD', 'EUR', 'GBP', 'AED', 'CAD', 'AUD', 'SGD', 'SAR', 'JPY'];
        }

        // Guarantee base currency is always included
        if (!in_array($base, $codes, true)) {
            array_unshift($codes, $base);
        }

        $rates = $this->getRates();
        $result = [];
        foreach ($codes as $code) {
            if (isset(self::SUPPORTED_CURRENCIES[$code])) {
                $meta = self::SUPPORTED_CURRENCIES[$code];
                $meta['rate'] = (float) ($rates[$code] ?? $meta['default_rate']);
                $result[$code] = $meta;
            }
        }

        return $result;
    }

    /**
     * Retrieve stored or default exchange rates relative to the Base Currency.
     *
     * @return array<string, float>
     */
    public function getRates(): array
    {
        $base = $this->getBaseCurrency();
        $raw = Setting::get('currency_rates');
        $stored = [];
        if (!empty($raw)) {
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $k => $v) {
                    $stored[strtoupper((string) $k)] = (float) $v;
                }
            }
        }

        $rates = [];
        foreach (self::SUPPORTED_CURRENCIES as $code => $meta) {
            if ($code === $base) {
                $rates[$code] = 1.0;
            } else {
                $rates[$code] = isset($stored[$code]) && $stored[$code] > 0
                    ? $stored[$code]
                    : (float) $meta['default_rate'];
            }
        }

        return $rates;
    }

    /**
     * Get specific exchange rate for a currency code relative to Base Currency.
     */
    public function getRate(string $code): float
    {
        $code = strtoupper($code);
        $base = $this->getBaseCurrency();
        if ($code === $base) {
            return 1.0;
        }

        $rates = $this->getRates();
        return (float) ($rates[$code] ?? (self::SUPPORTED_CURRENCIES[$code]['default_rate'] ?? 1.0));
    }

    /**
     * Get the currency symbol for a currency code.
     */
    public function getSymbol(string $code): string
    {
        $code = strtoupper($code);
        return self::SUPPORTED_CURRENCIES[$code]['symbol'] ?? '₹';
    }

    /**
     * Get the active patron's selected currency code.
     */
    public function getUserCurrency(): string
    {
        $base = $this->getBaseCurrency();
        if (! $this->isMultiCurrencyEnabled()) {
            return $base;
        }

        $selected = Session::get('currency') 
            ?: (request() ? request()->cookie('currency') : null);

        if ($selected) {
            $selected = strtoupper((string) $selected);
            $enabled = $this->getEnabledCurrencies();
            if (isset($enabled[$selected])) {
                return $selected;
            }
        }

        return $base;
    }

    /**
     * Retrieve full metadata of the user's active currency.
     *
     * @return array
     */
    public function getUserCurrencyData(): array
    {
        $code = $this->getUserCurrency();
        return $this->getCurrencyMeta($code);
    }

    /**
     * Retrieve metadata for a single currency code.
     */
    public function getCurrencyMeta(string $code): array
    {
        $code = strtoupper($code);
        $meta = self::SUPPORTED_CURRENCIES[$code] ?? self::SUPPORTED_CURRENCIES['INR'];
        $meta['rate'] = $this->getRate($code);
        return $meta;
    }

    /**
     * Persist user's preferred currency in Session and return Cookie instance for response chaining.
     */
    public function setUserCurrency(string $code): bool
    {
        $code = strtoupper(trim($code));
        $enabled = $this->getEnabledCurrencies();

        if (!isset($enabled[$code])) {
            return false;
        }

        Session::put('currency', $code);
        return true;
    }

    /**
     * Convert an amount between currencies.
     *
     * @param float|int|null $amount
     * @param string|null $to Target currency code (defaults to patron's current currency)
     * @param string|null $from Source currency code (defaults to store's Base Currency)
     * @return float
     */
    public function convert(float|int|null $amount, ?string $to = null, ?string $from = null): float
    {
        $amount = (float) ($amount ?? 0);
        $from = strtoupper($from ?: $this->getBaseCurrency());
        $to = strtoupper($to ?: $this->getUserCurrency());

        if ($from === $to) {
            return $amount;
        }

        $rateFrom = $this->getRate($from);
        $rateTo   = $this->getRate($to);

        if ($rateFrom <= 0) {
            $rateFrom = 1.0;
        }

        // Amount in Base Currency = Amount / rateFrom
        // Converted Amount = Amount in Base * rateTo
        $converted = ($amount / $rateFrom) * $rateTo;
        $decimals = self::SUPPORTED_CURRENCIES[$to]['decimals'] ?? 2;

        return round($converted, $decimals);
    }

    /**
     * Format a monetary value with proper symbols, thousand-separators, and precision.
     *
     * @param float|int|null $amount Numeric value in the target currency
     * @param string|null $currency Currency code to format as (defaults to patron's currency)
     * @param bool $withSymbol Whether to prefix with symbol
     * @return string
     */
    public function format(float|int|null $amount, ?string $currency = null, bool $withSymbol = true): string
    {
        $amount   = (float) ($amount ?? 0);
        $currency = strtoupper($currency ?: $this->getUserCurrency());
        $meta     = self::SUPPORTED_CURRENCIES[$currency] ?? self::SUPPORTED_CURRENCIES['INR'];
        $decimals = $meta['decimals'];
        $symbol   = $meta['symbol'];

        $formatted = number_format($amount, $decimals);

        return $withSymbol ? ($symbol . $formatted) : $formatted;
    }

    /**
     * Convert from base currency to patron's currency and format in one step.
     *
     * @param float|int|null $baseAmount
     * @param string|null $targetCurrency
     * @param bool $withSymbol
     * @return string
     */
    public function formatFromBase(float|int|null $baseAmount, ?string $targetCurrency = null, bool $withSymbol = true): string
    {
        $baseAmount = (float) ($baseAmount ?? 0);
        $targetCurrency = strtoupper($targetCurrency ?: $this->getUserCurrency());
        $converted = $this->convert($baseAmount, $targetCurrency, $this->getBaseCurrency());
        return $this->format($converted, $targetCurrency, $withSymbol);
    }

    /**
     * Shape frontend configuration payload for window.currencyConfig.
     *
     * @return array
     */
    public function getJsConfig(): array
    {
        $userCode = $this->getUserCurrency();
        $meta     = $this->getCurrencyMeta($userCode);

        return [
            'code'         => $meta['code'],
            'symbol'       => $meta['symbol'],
            'name'         => $meta['name'],
            'flag'         => $meta['flag'],
            'rate'         => $meta['rate'],
            'decimals'     => $meta['decimals'],
            'locale'       => $meta['locale'],
            'baseCurrency' => $this->getBaseCurrency(),
            'multiEnabled' => $this->isMultiCurrencyEnabled(),
        ];
    }
}
