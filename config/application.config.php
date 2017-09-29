<?php
/**
 * config/application.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

return [

    'application' => [
        'env' => 'production',
    ],

    'modules' => require __DIR__ . '/modules.config.php',

    'module_listener_options' => [

        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],

        'config_cache_enabled' => true,

        'config_cache_key' => 'application.config.cache',

        'module_map_cache_enabled' => true,

        'module_map_cache_key' => 'application.module.cache',

        'cache_dir' => __DIR__ . '/./../data/cache/',
    ],
];
