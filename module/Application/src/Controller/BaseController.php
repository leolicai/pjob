<?php
/**
 * BaseController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application\Controller;


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

}