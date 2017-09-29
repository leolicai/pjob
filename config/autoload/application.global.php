<?php
/**
 * application.global.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

return [

    'logger' => [
        'writers' => [
            'default' => [
                'options' => [
                    'stream' => __DIR__ . '/../../data/logs/php-log-' . date('Ymd') . '.txt',
                ],
                'filters' => [
                    'priority' => [
                        'options' => [
                            'priority' => 7,
                        ],
                    ],
                ],
            ],
        ],
    ],
];