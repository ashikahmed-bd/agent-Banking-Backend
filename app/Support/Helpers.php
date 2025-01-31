<?php

use Illuminate\Support\Facades\Storage;

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
