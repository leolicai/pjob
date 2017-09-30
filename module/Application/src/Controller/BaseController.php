<?php
/**
 * BaseController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application\Controller;


use Zend\Http\Response;
use Zend\Mvc\Console\Controller\AbstractConsoleController;


/**
 * Class BaseController
 * @package Application\Controller
 *
 * @method \Application\Controller\Plugin\AppLoggerPlugin appLogger()
 * @method \Application\Controller\Plugin\AppConfigPlugin appConfig()
 */
class BaseController extends AbstractConsoleController
{


    /**
     * @param array $result
     * @return bool|Response
     */
    protected function checkRequestResult($result)
    {
        $response = $result['response'];

        if (! $response instanceof Response) {
            $request = $result['request'];
            $this->appLogger()->err(
                __METHOD__ . PHP_EOL .
                'Request Failed!' . PHP_EOL .
                $request->getHeaders()->toString()
            );
            return false;
        }

        if (! $response->isSuccess()) {
            $request = $result['request'];
            $this->appLogger()->err(
                __METHOD__ . PHP_EOL .
                'Response Failed:' . $response->getReasonPhrase()  . PHP_EOL .
                'Request headers' . PHP_EOL .
                $request->getHeaders()->toString() .
                'Response headers' . PHP_EOL .
                $response->getHeaders()->toString()
            );
            return false;
        }

        return $response;
    }
}