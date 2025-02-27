<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;

if (! function_exists('logo')) {
    function logo(): string
    {
        return Storage::disk(config('app.disk'))->url('logo.svg');
    }
}


if (!function_exists('client_url')) {
    function client_url($value): string
    {
        return URL::format(config('app.client_url'), $value);
    }
}


if (!function_exists('formatDate')) {
    function formatDate($date): string
    {
        return Carbon::parse($date)->format('d M Y');
    }
}


if (!function_exists('NumberFormat')) {
    function NumberFormat($number): string
    {
        return Number::format($number);
    }
}
