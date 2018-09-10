<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Timezone Display
    |--------------------------------------------------------------------------
    |
    | If no timezone is specified to conversion functions, this timezone will
    | be used, perfect for converting to a single timezone.
    |
    */
    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Session Locale
    |--------------------------------------------------------------------------
    |
    | If true, check for current locale under 'locale' key in the session.
    | If false, fetch current locale using App::getLocale();
    |
    */
    'session_locale' => false,

    /*
    |--------------------------------------------------------------------------
    | Default Display Format
    |--------------------------------------------------------------------------
    |
    | This is the default format used when converting from storage.
    |
    */
    'format' => 'Y-m-d H:i:s',
];
