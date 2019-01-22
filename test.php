<?php
/**
 * Created by PhpStorm.
 * User: leishuanghe
 * Date: 2018/8/2
 * Time: 下午9:56
 */

namespace app\modules\common\services;

use app\modules\common\services\JavaInterfaceService;

class SmsDayuService
{
    //通知
    const SMS_140521046 = '5bf657e2afa03f9490489791'; //原价购中签通知
    const SMS_140736091 = '5bf657e2afa03f9490489791'; //原价购中签通知-国际

    const SMS_141615002 = '5bf65767afa03f949048978f'; //原价购发货通知
    const SMS_141580016 = '5bf65767afa03f949048978f'; //原价购发货通知-国际

    const SMS_142949783 = '5bf6572bafa03f949048978e'; //密码发送

    /**
     * 调用java-api请求参数
     */
    const MSG_APPID_PHP = 1;        //请求方发送的应用为PHP
    const MSG_APPID_H5 = 2;         //请求方发送的应用为
    const MSG_DELAY_TRUE = 1;       //发送延时
    const MSG_DELAY_FALSE = 0;      //发送不延时
    const MSG_DELAY_TIME = 0;       //延时时间
    const MSG_AREA_CODE = '86';     //区号
    const MSG_INTER_TRUE = 1;       //国际
    const MSG_INTER_FALSE = 0;      //国内
    const MSG_BUCK_TRUE = 1;        //批量
    const MSG_BUCK_FALSE = 0;       //不批量

    private static $instance;

    /**
     * 模板类型
     * @var array
     */
    public static $templateCategory = [
        '1' => '验证码',
        '3' => '推广(营销短信)',
        '2' => '通知',
    ];

    public static $type = [
        'hupu' => '虎扑',
        'aliInternet' => '阿里国际',
        'ali' => '阿里国内'
    ];

    /**
     * @return
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 发送短信
     * @param int $mobile 手机号
     * @param string $templateCode 模板号
     * @param array $params 模板参数
     * @param int $appId 请求方发送的应用id 1PHP 2H5 默认1
     * @param int $delay 是否延时 0不延时1延时 默认0
     * @param int $delayTime 延时时间
     * @param string $areaCode 区号
     * @param int $inter 是否国际 0国内1国际 默认0
     * @param int $bulk 是否批量 0否1是 默认0
     * @return mixed
     */
    public function sendSms($mobile,
                            $templateCode,
                            $params,
                            $areaCode = self::MSG_AREA_CODE,
                            $inter = self::MSG_INTER_FALSE,
                            $appId = self::MSG_APPID_PHP,
                            $delay = self::MSG_DELAY_FALSE,
                            $delayTime = self::MSG_DELAY_TIME,
                            $bulk = self::MSG_BUCK_FALSE)
    {
        $uri = '/api/v1/sms/sendSingle';
        $requestUrl = 'http://ms.poizon.com' . $uri;
        $data = [
            "appId" => $appId,
            "params" => empty($params) ? '' : json_encode($params),
            "time" => time(),
            "delay" => $delay,
            "delayTime" => $delayTime,
            "areaCode" => $areaCode,
            "inter" => $inter,
            "bulk" => $bulk,
            "mobile" => $mobile,
            "templateCode" => $templateCode
        ];
        $token = $this->generateSmsApiToken($data, $uri);
        $data['token'] = $token;
        $res = $this->httpCurl($requestUrl, $data);
        if ($res && $res['success']) {
            return $this->jsonResponse('OK', $res['message'], $res['data']);
        }
        return $this->jsonResponse('FAIL', $res['message']);

    }

    public function sendSmsOld($phone, $template, $templateParam)
    {
        $params = array();

        // *** 需用户填写部分 ***

        //请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = 'LTAI5AfRXOyP8bez';
        $accessKeySecret = 'Oxh93cJ5HMV3Boj9XwKt41bybAPniA';

        //必填: 短信接收号码
        $params["PhoneNumbers"] = "00{$phone}";

        //必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "毒App";

        //必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $template;

        //可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $templateParam;

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 此处可能会抛出异常，注意catch
        try {
            $response = $this->request(
                $accessKeyId,
                $accessKeySecret,
                "dysmsapi.aliyuncs.com",
                array_merge($params, array(
                    "RegionId" => "cn-hangzhou",
                    "Action" => "SendSms",
                    "Version" => "2017-05-25",
                ))
            );
            $response = json_encode($response);

            \Yii::warning(json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'sms_dayu');
            \Yii::warning($response, 'sms_dayu');

            return $response;
        } catch (\Exception $e) {

            return $e->getMessage();
        }


        return $content;
    }

    public function getHost()
    {
        return YII_ENV_PROD ? 'http://ms.poizon.com/api/v1/smsback' : 'http://121.199.21.211:9001/api/v1/smsback';
    }

    /**
     * 获取短信模板列表
     * @param $lastId
     * @param $limit
     * @param $supplier
     * @return array
     */
    public function getTemplateList($page, $limit, $content, $category)
    {
        $uri = "/template/list/?pageNum={$page}&pageSize={$limit}&templateContent=" . urlencode($content) . "&templateType={$category}";

        $requestUrl = $this->getHost() . $uri;
        $response = JavaInterfaceService::sendRequest($requestUrl);

        $result = ['list' => [], 'count' => 0];
        if ($response && ($response['status'] == 0)) {
            $result['list'] = $response['data']['list'];
            $result['count'] = $response['data']['page']['total'];
        }

        return $result;
    }

    /**
     * 删除短信模板
     * @param $templateId
     * @return array
     */
    public function deleteTemplate($templateId)
    {
        $uri = "/template/delete/{$templateId}";
        $requestUrl = $this->getHost() . $uri;
        $data = [
            'id' => $templateId,
        ];
        $response = JavaInterfaceService::sendRequest($requestUrl, $data);

        return $response;
    }

    /**
     * 修改短信模板状态
     * @param $templateId
     * @return array
     */
    public function changeTemplateStatus($templateId, $status)
    {
        $uri = '/template/update';
        $requestUrl = $this->getHost() . $uri;
        $data = [
            'id' => $templateId,
            'status' => $status
        ];
        $response = JavaInterfaceService::sendRequest($requestUrl, $data);

        return $response;
    }

    /**
     * 获取短信模板详情
     * @param $templateId
     * @return array
     */
    public function templateDetail($templateId)
    {
        $uri = "/template/get?templateId={$templateId}";
        $requestUrl = $this->getHost() . $uri;
        $response = JavaInterfaceService::sendRequest($requestUrl);

        if ($response && ($response['status'] == 0)) {
            $response['data'] = $response['data']['entity'];
        }

        return $response;
    }

    /**
     * 添加/编辑短信模板
     * @param $content
     * @param $supplier
     * @return array
     */
    public function editTemplate($content, $aliDomesticCode, $aliInternationalCode, $hupuCode, $mwCode, $comment, $category, $templateId = 0)
    {
        $data = [
            'templateContent' => $content,
            'aliDomesticCode' => $aliDomesticCode,
            'aliInternationalCode' => $aliInternationalCode,
            'hupuCode' => $hupuCode,
            'mwCode' => $mwCode,
            'templateType' => $category,
            'comment' => $comment,
        ];

        if ($templateId) {
            $uri = '/template/update';
            $data['id'] = $templateId;
        } else {

            $uri = '/template/save';
        }
        $requestUrl = $this->getHost() . $uri;
        $response = JavaInterfaceService::sendRequest($requestUrl, $data);

        return $response;
    }

    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false)
    {
        $apiParams = array_merge(array(
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if ($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }

    /**
     * json返回请求
     * @param $code
     * @param string $message
     * @param array $data
     * @return string
     */
    public function jsonResponse($code = 0, $message = '', $data = array())
    {
        $result = array(
            'Code' => $code,
            'Message' => $message,
            'Data' => $data
        );
        return json_encode($result);
    }

    /**
     * 生成api请求token
     * @param $data array 请求参数数组
     * @param string string $uri 请求uri
     * @return bool|string
     */
    private function generateSmsApiToken($data, $uri = '')
    {
        if (!is_array($data)) {
            return false;
        }
        $data['key'] = 'Theduapp123456!PHP';
        $data['uri'] = $uri;
        if (!empty($data['params'])) {
            $paramsArr = json_decode($data['params'], true);
            unset($data['params']);
            $data = array_merge($data, $paramsArr);
        } else {
            unset($data['params']);
        }
        ksort($data);
        $sortedParams = '';
        foreach ($data as $key => $value) {
            $sortedParams .= $key . "=" . $value . "&";
        }
        $sortedParams = rtrim($sortedParams, '&');
        $token = md5($sortedParams);
        return $token;
    }

    /**
     * httpCurl For JavaApi
     * @param string $url
     * @param array $data 请求参数
     * @param int $timeout 执行超时时间
     * @param array $header 请求头部
     * @return mixed
     */
    public function httpCurl($url, array $data = array(), $timeout = 10, array $header = array())
    {
        $data = json_encode($data);
        $ch = curl_init();
        if (empty($header)) {
            $header = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        if (stripos($url, 'https://') !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        return $output = empty($result) ? false : json_decode($result, true);
    }

}