<?php
/**
 * ZdmController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/29
 * Version: 1.0
 */

namespace Application\Controller;


use Application\Service\NetWorkService;
use Zend\Http\Header\SetCookie;
use Zend\Http\Response;

class ZdmController extends BaseController
{

    public function indexAction()
    {
        $doAction = $this->params()->fromRoute('doAction');

        $result = null;
        switch ($doAction) {
            case 'checkin':
                $result = $this->checkinZdm();
                break;
            default:
                break;
        }

        return $result;
    }


    /**
     * Do checkin
     */
    public function checkinZdm()
    {
        $cookies = $this->getLoginCookie();
        if (!($cookies instanceof \ArrayIterator || $cookies instanceof \Traversable)) {
            return;
        }
        $this->appLogger()->debug("Got prepare login cookies");
        sleep(1);

        $cookies = $this->getLoginedCookie($cookies);
        if (!($cookies instanceof \ArrayIterator || $cookies instanceof \Traversable)) {
            return;
        }
        $this->appLogger()->debug("Got login cookies");
        sleep(1);

        $response = $this->sendCheckInTask($cookies);
        if ($response instanceof Response) {
            $this->appLogger()->debug(__METHOD__ . PHP_EOL . $response->getBody());
        }
    }



    /**
     * @param SetCookie[] $cookies
     * @return bool|Response
     */
    private function sendCheckInTask($cookies)
    {
        $config = $this->appConfig()->get('zdm.checkin.path.checkin');
        if (empty($config['url']) || empty($config['referer'])) {
            return false;
        }

        $time = time();

        $callback = 'callback=jQuery1' . $time . $time . '_' . $time . substr($time, 0, 3) . '&_=' . $time . substr($time, -3);

        $headers = array_merge(
            NetWorkService::HeaderAcceptAllString(),
            NetWorkService::HeaderRefererString($config['referer'])
        );

        $result = NetWorkService::HttpGetRequest($config['url'] . $callback, $headers, $cookies);

        return $this->checkRequestResult($result);
    }



    /**
     * @param SetCookie[] $cookies
     * @return bool|SetCookie[]
     */
    private function getLoginedCookie($cookies)
    {
        $config = $this->appConfig()->get('zdm.checkin.path.login');
        if (empty($config['url']) || empty($config['referer'])) {
            return false;
        }

        $headers = array_merge(
            NetWorkService::HeaderXRequestWithString(),
            NetWorkService::HeaderPostContentTypeString(),
            NetWorkService::HeaderAcceptJsonString(),
            NetWorkService::HeaderRefererString($config['referer'])
        );

        $user = $this->appConfig()->get('zdm.checkin.user');
        $username = @$user['username'];
        $passport = @$user['passport'];

        $data = [
            'username' => $username,
            'password' => $passport,
            'rememberme' => '1',
            'captcha' => '',
            'redirect_to' => '',
            'geetest_challenge' => '',
            'geetest_validate' => '',
            'geetest_seccode' => '',
        ];

        $result = NetWorkService::HttpPostRequest($config['url'], $data, $headers, $cookies);

        $response = $this->checkRequestResult($result);
        if ($response instanceof Response) {
            return $response->getCookie();
        }
        return false;
    }



    /**
     * @return bool|SetCookie[]
     */
    private function getLoginCookie()
    {
        $config = $this->appConfig()->get('zdm.checkin.path.prelogin');
        if (empty($config['url']) || empty($config['referer'])) {
            return false;
        }

        $headers = array_merge(
            NetWorkService::HeaderAcceptTextHtmlString(),
            NetWorkService::HeaderRefererString($config['referer'])
        );

        $result = NetWorkService::HttpGetRequest($config['url'], $headers);

        $response = $this->checkRequestResult($result);
        if ($response instanceof Response) {
            return $response->getCookie();
        }
        return false;
    }

}