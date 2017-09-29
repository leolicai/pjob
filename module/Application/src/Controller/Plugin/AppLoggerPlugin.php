<?php
/**
 * AppLoggerPlugin.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Application\Controller\Plugin;


use Logger\Service\LoggerService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class AppLoggerPlugin extends AbstractPlugin
{

    /**
     * @var LoggerService
     */
    private $loggerService;


    /**
     * AppLoggerPlugin constructor.
     * @param LoggerService $loggerService
     */
    public function __construct(LoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
    }


    /**
     * @param null $data
     * @param int $priority
     * @return LoggerService
     */
    public function __invoke($data = null, $priority = 7)
    {
        if (null == $data) {
            return $this->loggerService;
        } else {
            $this->loggerService->mixed($data, $priority);
        }
        return $this->loggerService;
    }

}