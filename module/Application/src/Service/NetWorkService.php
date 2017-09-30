<?php
/**
 * NetWorkService.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/27
 * Version: 1.0
 */

namespace Application\Service;


use Zend\Http\Client;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;


class NetWorkService
{

    static $HTTP_INFO;

    /**
     * @param string $url
     * @param array $headers
     * @param SetCookie[] $cookies
     * @return array
     */
    public static function HttpGetRequest($url, $headers = [], $cookies = null)
    {
        return self::HttpRequest($url, $headers, $cookies);
    }


    /**
     * @param string $url
     * @param mixed $data
     * @param array $headers
     * @param SetCookie[] $cookies
     * @return array
     */
    public static function HttpPostRequest($url, $data, $headers = [], $cookies = null)
    {
        $body = null;
        if (is_array($data)) {
            $body = new Parameters($data);
        }
        return self::HttpRequest($url, $headers, $cookies, $body);
    }


    /**
     * @param string $url
     * @param array $headers
     * @param SetCookie[] $cookies
     * @param string $body
     * @return array
     */
    public static function HttpRequest($url, $headers = [], $cookies = null, $body = null)
    {
        $method = null;
        if (null === $body) {
            $method = Request::METHOD_GET;
        } else {
            $method = Request::METHOD_POST;
        }

        $headers = array_merge(self::GenerateSimpleHeaders(), $headers);

        $requestHeaders = new Headers();

        $host = parse_url($url, PHP_URL_HOST);
        foreach (self::HeaderHostString($host) as $k => $v) {
            if (empty($k) || empty($v)) continue;
            $requestHeaders->addHeaderLine($k, $v);
        }

        foreach ($headers as $k => $v) {
            if (empty($k) || empty($v)) continue;
            $requestHeaders->addHeaderLine($k, $v);
        }

        if (null !== $cookies) {
            $_cookies = [];
            foreach ((array)$cookies as $cookie) {
                if ($cookie instanceof SetCookie) {
                    if (!$cookie->isExpired()) {
                        $_cookies[] = $cookie;
                    }
                }
            }
            if (count($_cookies)) {
                $requestHeaders->addHeader(Cookie::fromSetCookieArray($_cookies));
            }
        }

        $request = new Request();
        $request->setUri($url);
        $request->setMethod($method);
        $request->setHeaders($requestHeaders);


        if ($method == Request::METHOD_POST) {
            if ($body instanceof Parameters) {
                $request->setPost($body);
            } else {
                $request->setContent($body);
            }
        }

        $client = new Client();
        $client->setRequest($request);
        $client->setOptions([
            'maxredirects' => 0,
            'timeout' => 30,
        ]);

        //if (null !== $cookies) {
            //$client->addCookie($cookies);
        //}

        $response = $client->send();
        return [
            'request' => $client->getRequest(),
            'response' => $response,
        ];
    }


    public static function  GenerateSimpleHeaders($responseType = '')
    {
        $headers = [];
        if ('text/html' == $responseType) {
            foreach (self::HeaderAcceptTextHtmlString() as $k => $v) {
                $headers[$k] = $v;
            }
        }

        if('application/json' == $responseType) {
            foreach (self::HeaderAcceptJsonString() as $k => $v) {
                $headers[$k] = $v;
            }
        }

        foreach(self::HeaderAcceptLanguageString() as $k => $v) {
            $headers[$k] = $v;
        }

        foreach(self::HeaderAcceptEncodingString() as $k => $v) {
            $headers[$k] = $v;
        }

        foreach(self::HeaderUserAgentString() as $k => $v) {
            $headers[$k] = $v;
        }

        foreach(self::HeaderConnectionString() as $k => $v) {
            $headers[$k] = $v;
        }

        return $headers;
    }


    public static function HeaderGetUrlStringLine($url, $httpVersion = 'HTTP/1.1')
    {
        return 'GET ' . $url . ' ' . $httpVersion;
    }

    public static function HeaderCookieString($cookie)
    {
        return ['Cookie' => $cookie];
        //return 'Cookie: ' . $cookie;
    }

    public static function HeaderHostString($host)
    {
        return ['Host' => $host];
        //return 'Host: ' . $host;
    }

    public static function HeaderRefererString($referer)
    {
        return ['Referer' => $referer];
        //return 'Referer: ' . $referer;
    }

    public static function HeaderUserAgentString()
    {
        return ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0'];
        //return 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:52.0) Gecko/20100101 Firefox/52.0';
    }

    public static function HeaderAcceptTextHtmlString()
    {
        return ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'];
        //return 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    }

    public static function HeaderAcceptAllString()
    {
        return ['Accept' => '*/*'];
        //return 'Accept: */*';
    }

    public static function HeaderAcceptJsonString()
    {
        return ['Accept' => 'application/json, text/javascript, */*; q=0.01'];
        //return 'Accept: application/json, text/javascript, */*; q=0.01';
    }

    public static function HeaderAcceptLanguageString()
    {
        return ['Accept-Language' => 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3'];
        //return 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3';
    }

    public static function HeaderAcceptEncodingString()
    {
        return ['Accept-Encoding' => 'gzip, deflate'];
        //return 'Accept-Encoding: gzip, deflate';
    }

    public static function HeaderConnectionString()
    {
        return ['Connection' => 'keep-alive'];
        //return 'Connection: keep-alive';
    }

    public static function HeaderXRequestWithString()
    {
        return ['X-Requested-With' => 'XMLHttpRequest'];
        //return 'X-Requested-With: XMLHttpRequest';
    }

    public static function HeaderPostContentTypeString($charset = 'UTF-8')
    {
        return ['Content-Type' => 'application/x-www-form-urlencoded; charset=' . $charset];
        //return 'Content-Type: application/x-www-form-urlencoded; charset=' . $charset;
    }
}