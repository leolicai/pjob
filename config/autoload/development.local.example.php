<?php
/**
 * development.local.example.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

return [

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => 'root',
                    'dbname' => 'test',
                    'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
                ],
            ],
        ],
    ],

];