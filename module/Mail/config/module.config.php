<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Mail;


$_TPL_SIMPLE = <<<EOF
Hi, There is a simple mail template.
EOF;


return [

    'service_manager' => [
        'factories' => [
            Service\MailService::class => Service\Factory\MailFactory::class,
        ],
        'aliases' => [
            'Mail' => Service\MailService::class,
        ],
    ],

    'mail' => [
        'smtp' => [
            'name' => 'MailService',
            'host' => '',
            'port' => 465,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => '',
                'password' => '',
                'ssl' => 'ssl',
            ],
        ],
        'contact' => 'name@example.com',
        'template' => [
            'simple' => $_TPL_SIMPLE,
        ],
    ],
];