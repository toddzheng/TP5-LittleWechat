<?php
/**
 * Created by 郑庆添.
 * User: 天呐
 * Date: 2017/9/26 0026
 * Time: 11:33
 */

namespace app\api\service;


use app\lib\exception\OrderException;
use app\api\model\Order;
use app\lib\exception\PayException;

class PayService
{
    private $orderID;
    private $orderNO;
    private $total_price;

    public function __construct($id)
    {
        if(empty($id)){
            throw new OrderException($msg='订单号不能为空',$errorCode=80003);
        }
        $this->orderID = $id;
    }

    public function pay(){
//        1:检测订单状态
        $this->checkOrderValidate();
//        2：生成客户端拉起微信支付需要的信息，如timeStamp，nonceStr，package（预支付交易会话标识），signType，paySign
        return $this->makeWxPreOrder();

    }

    protected function checkOrderValidate(){
        $order =Order::get($this->orderID);
        //订单号可能根本就不存在
        if(!$order){
            throw new OrderException();
        }
        //订单有可能已经被支付过
        if($order->status==2){
            echo '12';
            throw new OrderException($msg='该订单已经支付过了',$errorCode=80004);
        }
        //订单号确实是存在的，但是，订单号和当前用户是不匹配的
        $tokenUid = Token::getCurrentTokenVar('uid');
        if($tokenUid !=$order->user_id){
            throw new OrderException($msg='订单用户与Token用户不匹配',$errorCode=80004);
        }
        //如果通过三次检测，取出oderNo并返回true,
        $this->orderNO = $order->order_no;
        $this->total_price = $order->total_price;
        return true;

    }
    protected function makeWxPreOrder(){
        $orderParameter = $this->getOrderParameters();
        // 统一下单 获取prepay_id
        $prepayOrder=$this->unifiedOrder($orderParameter);
        //记录对应订单的prepay_id
        Order::where('id','=',$this->orderID)->update(['prepay_id' => $prepayOrder['prepay_id']]);
        return $this->getPaySignature($prepayOrder);
    }
    /**
     * 获取jssdk需要用到的数据
     * @return array jssdk需要用到的数据
     */
    protected function getOrderParameters(){
        $openid = Token::getCurrentTokenVar('openid');
        $ip = $this->getUserIp();
        $order=array(
            'body'=>'零食商贩',// 商品描述（需要根据自己的业务修改）
            'total_fee'=>intval($this->total_price*100),// 订单金额  以(分)为单位（需要根据自己的业务修改）
            'out_trade_no'=>$this->orderNO,// 订单号（需要根据自己的业务修改）
            'trade_type'=>'JSAPI',// JSAPI公众号支付
            'spbill_create_ip'=>$ip,//用户IP
            'openid'=>$openid// 获取到的openid
        );
        return $order;
    }
    protected function getUserIp(){
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "192.168.0.100";
        return $ip;
    }
    /**
     * 统一下单
     * @paramater  array $order 订单 必须包含支付所需要的参数 body(产品描述)、total_fee(订单金额)、out_trade_no(订单号)、trade_type(类型：JSAPI，NATIVE，APP)
     */
    public function unifiedOrder($order){
        // 获取配置项
        $nonce_str = md5(time() . mt_rand(0, 1000));
        $config=array(
            'appid'=>config('wx.Appid'),
            'mch_id'=>config('wx.MCHID'),
            'nonce_str'=>$nonce_str,
            'notify_url'=>config('wx.NOTIFY_URL')
        );
        // 合并配置数据和订单数据
        $data=array_merge($order,$config);
        // 生成签名
        $sign=$this->makeSign($data);
        $data['sign']=$sign;
        $xml=$this->toXml($data);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';//接收xml数据的文件
        $header[] = "Content-type: text/xml";//定义content-type为xml,注意是数组
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 兼容本地没有指定curl.cainfo路径的错误
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        const SSLCERT_PATH = '../cert/apiclient_cert.pem';
//        const SSLKEY_PATH = '../cert/apiclient_key.pem';
//        if($useCert == true){
//            //设置证书
//            //使用证书：cert 与 key 分别属于两个.pem文件
//            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
//            curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
//            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
//            curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
//        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            // 显示报错信息；终止继续执行
            throw new PayException($msg='curl发起支付时出错',$errerCode=90001);
        }
        curl_close($ch);
        $result=$this->toArray($response);
        // 显示错误信息
        if ($result['return_code']=='FAIL') {
            throw new PayException($msg=$result['return_msg'],$errerCode=90002);
        }
        $result['sign']=$sign;
        $result['nonce_str']=$nonce_str;
        return $result;
    }
    /**
     * 生成签名
     * @return 签名，
     */
    public function makeSign($data){
        // 去空 If no callback is supplied, all entries of input equal to FALSE (see converting to boolean) will be removed. 如果没有给出回调函数，所有的等于 FALSE 的元素将会被移除掉
        $data=array_filter($data);

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        $string_sign_temp=$string_a."&key=".config('wx.Appsecret');
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }
    /**
     * 输出xml字符
     * @throws
     **/
    public function toXml($data){
        if(!is_array($data) || count($data) <= 0){
            throw new PayException($msg='数组数据异常！');
        }
        $xml = "<xml>";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function toArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }

    protected function getPaySignature($prepayOrder){
        // 组合jssdk需要用到的数据
        $data=array(
            'appId'=>config('wx.Appid'), //appid
            'timeStamp'=>strval(time()), //时间戳
            'nonceStr'=>$prepayOrder['nonce_str'],// 随机字符串
            'package'=>'prepay_id='.$prepayOrder['prepay_id'],// 预支付交易会话标识
            'signType'=>'MD5'//加密方式
        );
        // 生成签名
        $data['paySign']=$this->makeSign($data);
        //appId没有必要返回到客户端
        unset($data['appId']);
        return $data;
    }
}