<?php

namespace app\index\controller;

use Exception;
use think\Session;

class Captcha
{
    protected $config = [
        'imageH'    => 24,
        'imageW'    => 100,
        'length'    => 4,
        'fontSize'  => 22,
        'fontAngle' => [-10, 10],
        'bgColors'  =>  [233,236,239],
        'ttfPath'   => __DIR__ . '/assets/ttfs/1.ttf'
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function __get($name)
    {
        return $this->config[$name];
    }

    public function __set($name, $value)
    {
        if(isset($this->config[$name])){
            $this->config[$name] = $value;
        }
        
    }

    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    public function captcha()
    {
        header('content-type:image/png');
        try{
        $image = imagecreatetruecolor($this->imageW, $this->imageH);
        $bgColor = imagecolorallocate($image, $this->bgColors[0], $this->bgColors[1], $this->bgColors[2]);
        imagefill($image, 0, 0, $bgColor);

        //描绘内容
        $data = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';

        $code ='';
        //生成验证码
        for($i=0;$i<$this->length;$i++){
            $fontColor = imagecolorallocate($image, rand(0, 120), rand(0,120), rand(0, 120));
            $fontContext = substr($data, rand(0, strlen($data)-1), 1);

            
            $x= $i* $this->fontSize + rand($this->fontSize/3, $this->imageW/$this->length - $this->fontSize);
            $y= rand($this->imageH*0.89, $this->imageH*0.97);

            $code .= $fontContext;

            imagettftext($image, $this->fontSize, rand($this->fontAngle[0], $this->fontAngle[1]), $x, $y, $fontColor, $this->ttfPath, $fontContext);

        }
        //设置session
        Session::set('verify_code', strtolower($code));

        //描绘点
        for($i=0;$i<($this->imageH*$this->imageW/5);$i++){
            $pointColor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
            imagesetpixel($image, rand(1, $this->imageW), rand(1, $this->imageH), $pointColor);
        }
        //描绘线
        for($i=0;$i<3;$i++){
            $lineColre = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
            imageline($image, rand(0, $this->imageW), rand(0, $this->imageH), rand(0, $this->imageW), rand(0, $this->imageH), $lineColre);
        }
        //输出图像
        imagepng($image);

        //销毁图像
        imagedestroy($image);
        }catch(Exception $e){
            echo $e;
        }
        exit;
    }
}