<?php
/**
 * TestController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application\Controller;


class TestController extends BaseController
{
    public function versionAction()
    {
        return 'version 1.0' . PHP_EOL;
    }

}