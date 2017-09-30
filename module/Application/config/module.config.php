<?php
/**
 * module.config.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application;


use Zend\ServiceManager\Factory\InvokableFactory;


return [

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\TestController::class => InvokableFactory::class,
            Controller\JdController::class => InvokableFactory::class,
            Controller\ZdmController::class => InvokableFactory::class,
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AppConfigPlugin::class => Controller\Plugin\Factory\AppConfigPluginFactory::class,
            Controller\Plugin\AppLoggerPlugin::class => Controller\Plugin\Factory\AppLoggerPluginFactory::class,
        ],
        'aliases' => [
            'appConfig' => Controller\Plugin\AppConfigPlugin::class,
            'appLogger' => Controller\Plugin\AppLoggerPlugin::class,
        ],
    ],



    'console' => [
        'router' => [
            'routes' => [

                'zdm' => [
                    'type' => 'simple',
                    'may_terminate' => true,
                    'options' => [
                        'route' => '<doAction> zdm',
                        'defaults' => [
                            'controller' => Controller\ZdmController::class,
                            'action' => 'index',
                        ],
                    ],
                ],

                'jd' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => '<doAction> jd',
                        'defaults' => [
                            'controller' => Controller\JdController::class,
                            'action' => 'index',
                        ],
                    ],
                ],

                'test' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'test',
                        'defaults' => [
                            'controller' => Controller\TestController::class,
                            'action' => 'index',
                        ],
                    ],
                ],

                /***
                'default' => [
                    'type' => 'catchall',
                    'options' => [
                        'route' => '',
                        'defaults' => [
                            'controller' => Controller\IndexController::class,
                            'action' => 'index',
                        ],
                    ],
                ],
                //*/

            ],
        ],
    ],


];