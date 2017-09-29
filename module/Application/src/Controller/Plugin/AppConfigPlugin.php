<?php
/**
 * AppConfigPlugin.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Application\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class AppConfigPlugin extends AbstractPlugin
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * AppConfigPlugin constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * Quickly get config
     *
     * @param $key
     * @param null $default
     *
     * @return array|mixed|null
     */
    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return $this->config;
        }

        $key = (string)$key;
        if (empty($key)) {
            return $default;
        }

        $config = $this->config;
        $keys = explode('.', $key);
        $matched = false;
        $keysCount = count($keys);
        $keysStep = 0;
        foreach($keys as $_key) {
            if (array_key_exists($_key, $config)) {
                $matched = true;
                $config = $config[$_key];
            } else {
                break;
            }
            $keysStep++;
        }

        if (!$matched) {
            return $default;
        }
        if($keysStep < $keysCount) {
            return $default;
        }

        return $config;
    }


    /**
     * @param null $key
     * @param null $default
     * @return $this|array|null
     */
    public function __invoke($key = null, $default = null)
    {
        if (null === $key) {
            return $this;
        } else {
            return $this->get($key, $default);
        }
    }

}