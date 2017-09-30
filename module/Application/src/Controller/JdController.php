<?php
/**
 * JdController.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application\Controller;


use Application\Service\NetWorkService;
use PHPHtmlParser\Dom;
use Zend\Http\Response;


class JdController extends BaseController
{

    const ACTION_CHECK_IN = 'checkin';

    public function indexAction()
    {
        $doAction = $this->params()->fromRoute('doAction', '');

        $result = null;
        switch ($doAction) {
            case self::ACTION_CHECK_IN:
                $result = $this->checkinJd();
                break;
            default:
                break;
        }

        return $result;
    }


    private function checkinJd()
    {
        $this->getLoginCookie();
    }


    private function getLoginCookie()
    {
        $path = $this->appConfig()->get('jd.checkin.path.login');

        $headers = array_merge(
            NetWorkService::HeaderAcceptTextHtmlString(),
            NetWorkService::HeaderRefererString($path['ref'])
        );

        $result = NetWorkService::HttpGetRequest($path['url'], $headers);
        $response = $this->checkRequestResult($result);
        if (! $response instanceof Response) {
            return;
        }
        $loginPageCookie = $response->getCookie();

        $html = iconv('GBK', 'UTF-8', $response->getBody());

        $dom = new Dom();
        $dom->loadStr($html, []);
        $inputs = $dom->find('input');

        $data = [];
        foreach ($inputs as $input) {
            $name = $input->getAttribute('name');
            if ('sa_token' == $name) { $data[$name] = $input->getAttribute('value'); }
            if ('uuid' == $name) { $data[$name] = $input->getAttribute('value'); }
            if ('pubKey' == $name) { $data[$name] = $input->getAttribute('value'); }
            if ('_t' == $name) { $data[$name] = $input->getAttribute('value'); }
            if ('loginType' == $name) { $data[$name] = 'f'; }
            if ('authcode' == $name) { $data[$name] = ''; }
            if ('chkRememberMe' == $name) { $data[$name] = ''; }
            if ('eid' == $name) { $data[$name] = 'MWZ2KUI6A3A2T5E7NZWGQDVFOENOYBKSD72OLI2XLCSZECT4VTXOPJMI3N3TOWRFCK4ACZH5IJKG3EWLP4L45L4I5U'; }
            if ('fp' == $name) { $data[$name] = '7362701eb82b108a5c88c62ecd2e321e'; }
        }
        $this->appLogger()->debug(
            __METHOD__ . PHP_EOL .
            'Got login page form.'
        );
        sleep(1);


        $data['seqSid'] = $this->getSeqSid();
        sleep(1);

        $path = $this->appConfig()->get('jd.checkin.path.logined');
        $this->appLogger()->debug($path['url']);
        $timer = time();
        $rStr = $timer . substr($timer, 0, 6);
        $url = str_replace(':uuid', $data['uuid'], $path['url']);
        $url = str_replace(':rand', $rStr, $url);
        $this->appLogger()->debug(
            __METHOD__ . PHP_EOL .
            'Post Login URL: ' . $url
        );

        $headers = array_merge(
            NetWorkService::HeaderAcceptTextHtmlString(),
            NetWorkService::HeaderXRequestWithString(),
            NetWorkService::HeaderRefererString($path['ref'])
        );

        $user = $this->appConfig()->get('jd.checkin.user');

        $data['loginname'] = $user['username'];
        $data['nloginpwd'] = $this->encriptPassword($user['passport'], $data['pubKey']);

        $this->appLogger()->mixed($data);

        $result = NetWorkService::HttpPostRequest($url, $data, $headers, $loginPageCookie);
        $response = $this->checkRequestResult($result);
        if (! $response instanceof Response) {
            return;
        }

        $body = $response->getBody();
        $this->appLogger()->debug(
            __METHOD__ . PHP_EOL .
            'Post login response:' . PHP_EOL .
            $body
        );

        echo $body . PHP_EOL;

        return $response->getCookie();
    }


    /**
     * @return string
     */
    private function getSeqSid()
    {
        $path = $this->appConfig()->get('jd.checkin.path.seqsid');

        $headers = array_merge(
            NetWorkService::HeaderAcceptAllString(),
            NetWorkService::HeaderRefererString($path['ref'])
        );

        $result = NetWorkService::HttpGetRequest($path['url'], $headers);
        $response = $this->checkRequestResult($result);
        if (! $response instanceof Response) {
            return '';
        }

        $body = $response->getBody();
        $js = explode(';', $body);
        $var = array_shift($js); //var _jdtdmap_sessionId="1573109991890306813"
        $seqSid = str_replace('"', '', substr($var, stripos($var, '=') + 1));
        if (empty($seqSid)) {
            $this->appLogger()->err(__METHOD__ . PHP_EOL . "Get seq id failed.");
            return '';
        }

        $this->appLogger()->debug(
            __METHOD__ . PHP_EOL .
            'Get seqSid: ' . $seqSid
        );

        return $seqSid;
    }


    private function encriptPassword($password, $pubKey)
    {
        $key = openssl_pkey_get_public($this->formatPublicKey($pubKey));
        openssl_public_encrypt($password, $encrypted, $key);
        return base64_encode($encrypted);
    }

    private function formatPublicKey($keyString)
    {
        $key = "-----BEGIN PUBLIC KEY-----\r\n" . $keyString . "\r\n-----END PUBLIC KEY-----";
        return wordwrap($key, 64, "\r\n", true);
    }

}