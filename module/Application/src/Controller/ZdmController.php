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
        $cookie = $this->getLoginCookie();
        if (false === $cookie) {
            return;
        }

        $this->appLogger()->debug("Got prepare login cookies");
        sleep(1);

        $cookie = $this->getLoginedCookie($cookie);
        if (false === $cookie) {
            return;
        }

        $this->appLogger()->debug("Got login cookies");
        sleep(1);

        $response = $this->sendCheckInTask($cookie);
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
        $rand1 = random_int(111, 999);
        $rand2 = random_int(111, 999);

        $rand_p1 = random_int(111111, 999999);
        $rand_p2 = random_int(1111111, 9999999);
        $rand_p3 = random_int(1111111, 9999999);

        $callback = 'callback=jQuery1' . $rand_p1 . $rand_p2 . $rand_p3 . '_' . $time . $rand1 . '&_' . $time . $rand2;

        $headers = array_merge(
            NetWorkService::HeaderAcceptAllString(),
            NetWorkService::HeaderRefererString($config['referer'])
        );

        $result = NetWorkService::HttpGetRequest($config['url'] . $callback, $headers, $cookies);

        return $this->checkResult($result);
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

        $response = $this->checkResult($result);
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

        $response = $this->checkResult($result);
        if ($response instanceof Response) {
            return $response->getCookie();
        }
        return false;
    }


    /**
     * @param array $result
     * @return bool|Response
     */
    private function checkResult($result)
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