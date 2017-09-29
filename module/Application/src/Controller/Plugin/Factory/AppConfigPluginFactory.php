<?php
/**
 * AppConfigPluginFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Application\Controller\Plugin\Factory;


use Application\Controller\Plugin\AppConfigPlugin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class AppConfigPluginFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AppConfigPlugin($container->get('config'));
    }

}