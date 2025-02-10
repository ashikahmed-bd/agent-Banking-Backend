<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

if (! function_exists('logo')) {
    function logo(): string
    {
        return Storage::disk(config('app.disk'))->url('logo.svg');
    }
}


if (! function_exists('getBusinessId')) {
    function getBusinessId()
    {
        return auth()->check() ?? auth()->user()->business->id;
    }
}


if (!function_exists('client_url')) {
    function client_url($value): string
    {
        return URL::format(config('app.client_url'), $value);
    }
}
