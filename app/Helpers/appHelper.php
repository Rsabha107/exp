<?php

// namespace App\Http\Helpers;

use App\Models\Cms\OrderStatus;
use App\Models\Event;
use App\Models\Sps\ItemStatus;
use App\Models\Sps\ProfileStatus;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


if (!function_exists('get_label')) {

    function get_label($label, $default, $locale = '')
    {
        if (Lang::has('labels.' . $label, $locale)) {
            return trans('labels.' . $label, [], $locale);
        } else {
            return $default;
        }
    }
}

if (!function_exists('getStatusIdByLabel')) {
    function getStatusIdByLabel(string $label): ?int
    {
        Log::info('getStatusIdByLabel called with label: ' . $label);
        $status_id = ItemStatus::where('title', $label)->pluck('id')->first();

        Log::info('getStatusIdByLabel found status_id: ' . ($status_id ?? 'null'));
        return $status_id ?? null;
    }
}

if (!function_exists('getProfileStatusIdByLabel')) {
    function getProfileStatusIdByLabel(string $label): ?int
    {
        $status_id = ProfileStatus::where('title', $label)->pluck('id')->first();

        return $status_id ?? null;
    }
}

// used for encrypting and decrypting data in a URL-safe manner
if (!function_exists('encryptUrlSafe') || !function_exists('decryptUrlSafe')) {
    function encryptUrlSafe(array $data): string
    {
        $json = json_encode($data);
        return rtrim(strtr(base64_encode(Crypt::encryptString($json)), '+/', '-_'), '=');
    }

    function decryptUrlSafe(string $value): array
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'));
        return json_decode(Crypt::decryptString($decoded), true);
    }
}

function superShortEncrypt(array $data): string
{
    $key = substr(hash('sha256', config('app.key')), 0, 16); // AES-128
    $iv = random_bytes(16);
    $json = json_encode($data);

    $cipher = openssl_encrypt($json, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    $payload = base64url_encode($iv . $cipher);

    return $payload;
}

function superShortDecrypt(string $payload): ?array
{
    $key = substr(hash('sha256', config('app.key')), 0, 16);
    $raw = base64url_decode($payload);

    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);

    $json = openssl_decrypt($cipher, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

    return json_decode($json, true);
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (strlen($data) + 3) % 4));
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $symbol = '$')
    {
        return $symbol . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('getQrCode')) {

    function getQrCode($id, $size)
    {
        // $qr_code = QrCode::size($size)->generate($id);
        $qr_code = base64_encode(QrCode::format('svg')->size($size)->errorCorrection('H')->generate($id));
        // $qr_code = base64_encode(QrCode::format('png')->size($size)->margin(1)->backgroundColor(255,255,255)->generate($id));
        // $qr_code = base64_encode(QrCode::size($size)->margin(1)->backgroundColor(255,255,255)->generate($id));

        return ($qr_code);
    }
}

if (!function_exists('time_range_segment')) {

    function time_range_segment($time_range, $segment)
    {
        if ($segment == 'from') {
            $return_segment = Str::substr($time_range, 0,  Str::position($time_range, "-") - 1);
            return $return_segment;
        } elseif ($segment == 'to') {
            $return_segment = Str::substr($time_range, Str::position($time_range, "-") + 1);
            return $return_segment;
        } else {
            return null;
        }
    }
}

/**
 * Generate initials from a name
 *
 * @param string $name
 * @return string
 */
if (!function_exists('generate')) {
    function generateInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                    mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8'
            );
        }
        return makeInitialsFromSingleWord($name);
    }
}

/**
 * Make initials from a word with no spaces
 *
 * @param string $name
 * @return string
 */
if (!function_exists('makeInitialsFromSingleWord')) {
    function makeInitialsFromSingleWord(string $name): string
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');
        }
        return mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
    }
}

/**
 * Get the id of an Order Status
 *
 * @param string $name
 * @return string
 */
if (!function_exists('gerOrderStatusId')) {
    function gerOrderStatusId(string $name): string
    {
        $order_status_id = OrderStatus::where('title', $name)->value('id');
        if ($order_status_id) {
            return $order_status_id;
        } else {
            return '';
        }
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $time = null, $format = null, $apply_timezone = true)
    {
        if ($date) {
            // Log::info('date: '.$date);
            // Log::info('time: '.$time);
            // Log::info('format: '.$format);
            $format = $format ?? get_php_date_format();
            $time = $time ?? '';

            $date = $time != '' ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::parse($date);

            // Log::info('date: '.$date);

            // if ($time !== '') {
            //     if ($apply_timezone) {
            //         $date->setTimezone(config('app.timezone'));
            //     }
            //     $format .= ' ' . $time;
            // }

            // Log::info($date->format($format));

            return $date->format($format);
        } else {
            return '-';
        }
    }
}

if (!function_exists('get_php_date_format')) {
    function get_php_date_format()
    {
        // $general_settings = get_settings('general_settings');
        $date_format = 'DD-MM-YYYY|d-m-Y';
        // $date_format = $general_settings['date_format'] ?? 'DD-MM-YYYY|d-m-Y';
        $date_format = explode('|', $date_format);
        return $date_format[1];
    }
}
