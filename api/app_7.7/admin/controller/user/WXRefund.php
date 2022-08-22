<?php
class WXRefund
{
  protected $SSLCERT_PATH ='/www/wwwroot/mt.aihuobao.cn/app/common/cert/apiclient_cert.pem';//证书

  protected $SSLKEY_PATH = '/www/wwwroot/mt.aihuobao.cn/app/common/cert/apiclient_key.pem';//证书
  
  
    // protected $SSLCERT_PATH ="/www/wwwroot/mt.aihuobao.cn/apiclient_cert.pem";//证书
    // protected $SSLKEY_PATH = "/www/wwwroot/mt.aihuobao.cn/apiclient_key.pem";//证书

  protected $opUserId = '1588038921';//商户号
  protected $key = 'OsyTKpaAV7saU50y2WQNANca9dMiLv8F';//API密钥
  protected $appId = 'wx18174f6e618741ec';//appId
  function __construct($refundFee, $totalFee,$outTradeNo, $outRefundNo)
  {
    //初始化退款类需要的变量
    $this->totalFee = $totalFee;//订单金额
    $this->refundFee = $refundFee;//退款金额
    $this->outTradeNo = $outTradeNo;//订单号
    $this->outRefundNo = $outRefundNo;//退款订单
  }
  /**
   * 通过微信api进行退款流程 唯一对外接口
   * @return string
   */
  public function refundApi()
  {
  
    $parma = array(
      'appid' => $this->appId,
      'mch_id' => $this->opUserId,
   
      'nonce_str' =>$this->createNoncestr(16),//这个是随机数 自己封装去吧。。。
      'out_refund_no' => $this->outRefundNo,
      'out_trade_no' => $this->outTradeNo,
      'total_fee' => intval($this->totalFee * 100),
      'refund_fee' => intval($this->refundFee * 100),
    );
    $parma['sign'] = $this->getSign($parma, $this->key);
    $xmldata = $this->arrayToXml($parma);
    $xmlresult = $this->postXmlSSLCurl($xmldata, 'https://api.mch.weixin.qq.com/secapi/pay/refund');
    dump($xmlresult);die;
    $result = $this->arrayToXml($xmlresult);
    return $result;
  }
  
      //作用：产生随机字符串，不长于32位
    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrs222wxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
  /**
   * 数组转xml
   * @param $arr
   * @return string
   */
   
    //   private function arrayToXml($arr) {
    //       dump($arr);die;
    //     $xml = "<root>";
    //     foreach ($arr as $key => $val) {
           
    //         if (is_array($val)) {
    //             $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
    //         } else {
    //             $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
    //         }
    //     }
    //     $xml .= "</root>";
    //     return $xml;
    // }


  private function arrayToXml($arr)
  {
    //  dump($arr);die;
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
      if (is_numeric($val)) {
        $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
      } else {
        $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
      }
    }
    $xml .= "</xml>";
   
    return $xml;
  }
  /**
   * 签名加密
   * @param $params
   * @param $key
   */
  protected function getSign($params, $key)
  {
    ksort($params, SORT_STRING);
    $unSignParaString = $this->formatQueryParaMap($params, false);
    return $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
  }
  /**
   * 排序
   * @param $paraMap
   * @param bool $urlEncode
   * @return bool|string
   */
  protected function formatQueryParaMap($paraMap, $urlEncode = false)
  {
    $buff = "";
    ksort($paraMap);
    foreach ($paraMap as $k => $v) {
      if (null != $v && "null" != $v) {
        if ($urlEncode) {
          $v = urlencode($v);
        }
        $buff .= $k . "=" . $v . "&";
      }
    }
    $reqPar = '';
    if (strlen($buff) > 0) {
      $reqPar = substr($buff, 0, strlen($buff) - 1);
    }
    return $reqPar;
  }
  /**
   * 需要使用证书的请求
   * @param $xml
   * @param $url
   * @param int $second
   * @return bool|mixed
   */
  protected function postXmlSSLCurl($xml, $url, $second = 30)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, $this->SSLCERT_PATH);
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, $this->SSLKEY_PATH);
    
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $data = curl_exec($ch);
    if ($data) {
      curl_close($ch);
      return $data;
    } else {
      $error = curl_errno($ch);
      echo "curl出错，错误码:$error" . "<br>";
      curl_close($ch);
      return false;
    }
  }
}