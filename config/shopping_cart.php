<?php

return [
    /*
     * ---------------------------------------------------------------
     * formatting
     * ---------------------------------------------------------------
     *
     * the formatting of shopping cart values
     */
    'format_numbers' => env('SHOPPING_FORMAT_VALUES', false),

    'decimals' => env('SHOPPING_DECIMALS', 0),

    'dec_point' => env('SHOPPING_DEC_POINT', '.'),

    'thousands_sep' => env('SHOPPING_THOUSANDS_SEP', ','),

    /*
     * ---------------------------------------------------------------
     * persistence
     * ---------------------------------------------------------------
     *
     * the configuration for persisting cart
     * 
     * This is a bit tricky. Always use session if user is something else than staff member and pos is installed.
     * This is later managed from CartController
     */
    //'storage' => env('IS_POS_CLOUD_MODE',false)?\App\Repositories\CartDBStorageRepository::class:null,
    'storage'=>null,

    /*
     * ---------------------------------------------------------------
     * events
     * ---------------------------------------------------------------
     *
     * the configuration for cart events
     */
    'events' => null,
];
