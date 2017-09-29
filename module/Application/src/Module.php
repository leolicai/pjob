<?php
/**
 * Module.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application;


use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;


class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'PHP Console Application default module';
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'checkin zdm'           => 'Use zdm controller do checkin action.',
        ];
    }


}