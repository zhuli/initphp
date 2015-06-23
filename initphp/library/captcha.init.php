<?php

/**
 * 扩展类库-验证码(增强版)
 * 
 * 功能特点：
 *     1. 生成并输出验证码图片，同时返回验证码，其存储方式以及校验由应用程序自行决定；
 *     2. 可自定义验证码图片大小，长度，字体及字体大小；
 * 
 * @version 0.1.0
 * @author Anran <id0612@gmail.com>
 */

defined('IS_INITPHP') || die('Access Denied!');

/**
 * 扩展类库-验证码
 */
class captchaInit {

    /** @var string $font 字体 */
    private $font = '';

    /** @var int $size 字体大小 */
    private $size = 18;

    /** @var int $width 宽度 */
    private $width = 120;

    /** @var int $height 高度 */
    private $height = 40;

    /** @var int $length 验证码长度 */
    private $length = 6;

    /**
     * 获取验证码
     * 
     * @return string
     */
    public function get($config = array()) {
        // 初始化
        if (!$this->init($config)) {
            return false;
        }
        unset($config);

        // 生成验证码
        $captcha = $this->generate_captcha($this->length);
        if (false === $captcha) {
            return false;
        }

        // 背景
        $img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($img, 0, $this->height, $this->width, 0, $color);

        // 线条
        for ($i = 0; $i < 6; ++$i) {
            $color = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline(
                    $img
                    , mt_rand(0, $this->width), mt_rand(0, $this->height)
                    , mt_rand(0, $this->width), mt_rand(0, $this->height)
                    , $color
            );
        }

        // 雪花
        for ($i = 0; $i < 100; ++$i) {
            $color = imagecolorallocate($img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }

        // 验证码
        $j = $this->width / $this->length;
        for ($i = 0; $i < $this->length; ++$i) {
            $color = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext(
                    $img, $this->size, mt_rand(-30, 30)
                    , $j * $i + mt_rand(1, 5), $this->height / 1.4
                    , $color, $this->font
                    , $captcha[$i]
            );
        }

        // 输出
        header('Content-type: image/png');
        imagepng($img);

        // 销毁
        imagedestroy($img);

        unset($img, $color, $i, $j);

        // 返回
        return strtolower($captcha);
    }

    /**
     * 初始化
     * 
     * @param array $config
     * 
     * @return bool
     */
    private function init($config) {
        if (is_array($config)) {
            if (isset($config['font']) && is_file($config['font']) && is_readable($config['font'])) {
                $this->font = $config['font'];
            } else {
                return false;
            }

            if (isset($config['size']) && ($config['size'] = (int) $config['size']) && 0 < $config['size']) {
                $this->size = $config['size'];
            }

            if (isset($config['width']) && ($config['width'] = (int) $config['width']) && 0 < $config['width']) {
                $this->width = $config['width'];
            }

            if (isset($config['height']) && ($config['height'] = (int) $config['height']) && 0 < $config['height']) {
                $this->height = $config['height'];
            }

            if (isset($config['length']) && ($config['length'] = (int) $config['length']) && 0 < $config['length']) {
                $this->length = $config['length'];
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * 生成验证码
     * 
     * @return string
     */
    private function generate_captcha($length = 6) {
        $length = intval($length);
        if (1 > $length || 50 < $length) {
            return false;
        }

        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'k', 'm',
            'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M',
            'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '2', '3', '4', '5', '6', '7', '8', '9'
        );

        $keys = array_rand($chars, $length);

        $captcha = '';

        foreach ($keys as $key) {
            $captcha .= $chars[$key];
        }

        unset($length, $chars, $keys, $key);

        return $captcha;
    }

}
