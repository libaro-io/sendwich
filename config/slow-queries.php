<?php
/************************************************************************************************
 * config for libaro/LaravelSlowQueries
 ************************************************************************************************/

return [

    /*
    |--------------------------------------------------------------------------
    | enabled: is the package enabled or not
    |--------------------------------------------------------------------------
    |
    |      - disabled by default
    |      - when enabled, these queries will be logged:
    |               - slow queries: see setting 'log_queries_slower_than'
    |               - queries on slow pages: see setting 'log_pages_slower_than'
    |               - queries on pages with many queries: see setting 'log_queries_more_than'
    |
    */

    'enabled' => env('LARAVEL_SLOW_QUERIES_ENABLED', false),


    /*
    |--------------------------------------------------------------------------
    | log_queries_slower_than
    |--------------------------------------------------------------------------
    |
    |      log queries that are slower than x,  IN MILISECONDS
    |
    */

    'log_queries_slower_than' => env('LARAVEL_SLOW_QUERIES_LOG_QUERIES_SLOWER_THAN', 500),


    /*
    |--------------------------------------------------------------------------
    | log_queries_more_than
    |--------------------------------------------------------------------------
    |
    |      log queries if there are more than x in the request/page
    |
    */

    'log_queries_more_than' => env('LARAVEL_SLOW_QUERIES_LOG_QUERIES_MORE_THAN', 50),


    /*
    |--------------------------------------------------------------------------
    | log_pages_slower_than
    |--------------------------------------------------------------------------
    |
    |      log queries if sum of all queries on the page is slower than x, in miliseconds
    |
    */

    'log_pages_slower_than' => env('LARAVEL_SLOW_QUERIES_LOG_PAGES_SLOWER_THAN', 1000),


    /*
    |--------------------------------------------------------------------------
    | middleware
    |--------------------------------------------------------------------------
    |
    |      which middleware to use to secure the admin pages of the package
    |           admin pages of the package = pages used to see the stats of the slow queries
    |
    */

    'middleware' => env('LARAVEL_SLOW_QUERIES_MIDDLEWARE', 'web'),


    /*
    |--------------------------------------------------------------------------
    | url_prefix
    |--------------------------------------------------------------------------
    |
    |      url to use for the package dashboard and admin pages
    |
    */

    'url_prefix' => env('LARAVEL_SLOW_QUERIES_URL_PREFIX', '/laravel-slow-queries'),


    /*
    |--------------------------------------------------------------------------
    | items_per_widget
    |--------------------------------------------------------------------------
    |
    |      number of items to show in a dashboard widget
    |
    */

    'items_per_widget' => env('LARAVEL_SLOW_QUERIES_ITEMS_PER_WIDGET', 5),


    /*
    |--------------------------------------------------------------------------
    | items_per_page
    |--------------------------------------------------------------------------
    |
    |      number of items to show per page
    |
    */

    'items_per_page' => env('LARAVEL_SLOW_QUERIES_ITEMS_PER_PAGE', 20),


    /*
    |--------------------------------------------------------------------------
    | default_date_range
    |--------------------------------------------------------------------------
    |
    |      default date range, IN DAYS
    |
    */


    'default_date_range' => env('LARAVEL_SLOW_QUERIES_DEFAULT_DATE_RANGE', 14),



    /*
    |--------------------------------------------------------------------------
    | default_date_range
    |--------------------------------------------------------------------------
    |
    |      default date range, IN DAYS
    |
    */

    'exclude_routes' => env('LARAVEL_SLOW_QUERIES_EXCLUDE_ROUTES', false)
];
