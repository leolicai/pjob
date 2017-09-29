<?php
/**
 * AppLoggerPluginFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Application\Controller\Plugin\Factory;


use Application\Controller\Plugin\AppLoggerPlugin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class AppLoggerPluginFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AppLoggerPlugin($container->get('AppLogger'));
    }

}