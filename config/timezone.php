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
    | Default Display Format
    |--------------------------------------------------------------------------
    |
    | This is the default format used when converting from storage.
    |
    */
    'format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Parse UK Dates
    |--------------------------------------------------------------------------
    |
    | If true, '/' will be replaced with '-' to parse as a UK date.
    | i.e. true = 10/05/2018 -> 2018-05-10
    | i.e. false = 10/05/2018 -> 2018-10-05
    */
    'parse_uk_dates' => false,
];
