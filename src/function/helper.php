<?php
/**
 * 辅助函数库
 *
 * @author Raj Luo
 */

if (!function_exists('ObtainTheClientIPAddress')) {
    /**
     * 获取客户端IP地址
     *
     * @param int $type 0：返回字符串IP地址，1：返回整型IP地址
     * @return mixed
     */
    function ObtainTheClientIPAddress($type = 0)
    {
        static $ip = NULL;

        if ($ip !== NULL) {
            return $ip[$type];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            $pos = array_search('unknown', $arr);

            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);

        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));

        $ip = array($ip, $long);

        return $ip[$type];
    }
}

if (!function_exists('GetsTheCurrentPageURL')) {
    /**
     * 获取当前页面URL
     *
     * @return string
     */
    function GetsTheCurrentPageURL()
    {
        $pageURL = 'http';

        if (!empty($_SERVER['HTTPS'])) {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        return $pageURL;
    }
}

if (!function_exists('GenerateOrderNumber')) {
    /**
     * 生成订单号
     *
     * @return string
     */
    function GenerateOrderNumber()
    {
        $outTradeNo = date('ymdHis');

        $outTradeNo .= rand(pow(10, (3 - 1)), pow(10, 3) - 1);

        return $outTradeNo;
    }
}

if (!function_exists('ByteUnitConversion')) {
    /**
     * 字节单位转换
     *
     * @param int $size 要计算的文件原始大小
     * @param string $unit 要转换成的单位（auto，kb, mb, gb, tb）
     * @param bool $suffix 转换完成的结果是否带单位字符
     * @return float|int|string|null
     */
    function ByteUnitConversion(int $size, $unit = 'auto', $suffix = true)
    {
        if (empty($size)) {
            return null;
        }

        $kb = 1024;       // Kilobyte
        $mb = 1024 * $kb; // Megabyte
        $gb = 1024 * $mb; // Gigabyte
        $tb = 1024 * $gb; // Terabyte

        switch ($unit) {
            //自动转换模式
            case 'auto':
                if ($size < $kb) {
                    $unitSuffix = ' B';

                } elseif ($size < $mb) {
                    $size = round($size / $kb, 2);
                    $unitSuffix = ' KB';

                } elseif ($size < $gb) {
                    $size = round($size / $mb, 2);
                    $unitSuffix = ' MB';

                } elseif ($size < $tb) {
                    $size = round($size / $gb, 2);
                    $unitSuffix = ' GB';

                } else {
                    $size = round($size / $tb, 2);
                    $unitSuffix = ' TB';
                }

                return ($suffix) ? $size . $unitSuffix : $size;

                break;

            //转换成KB
            case 'kb':
                $size = round($size / $kb, 2);

                $unitSuffix = ' KB';

                return ($suffix) ? $size . $unitSuffix : $size;

                break;

            //转换成MB
            case 'mb':
                $size = round($size / $mb, 2);

                $unitSuffix = ' MB';

                return ($suffix) ? $size . $unitSuffix : $size;

                break;

            //转换成GB
            case 'gb':
                $size = round($size / $gb, 2);

                $unitSuffix = ' GB';

                return ($suffix) ? $size . $unitSuffix : $size;

                break;

            //转换成TB
            case 'tb':
                $size = round($size / $tb, 2);

                $unitSuffix = ' TB';

                return ($suffix) ? $size . $unitSuffix : $size;

                break;

            //默认自动转换模式
            default :
                if ($size < $kb) {
                    $unitSuffix = ' B';

                } elseif ($size < $mb) {
                    $size = round($size / $kb, 2);

                    $unitSuffix = ' KB';

                } elseif ($size < $gb) {
                    $size = round($size / $mb, 2);

                    $unitSuffix = ' MB';

                } elseif ($size < $tb) {
                    $size = round($size / $gb, 2);

                    $unitSuffix = ' GB';

                } else {
                    $size = round($size / $tb, 2);

                    $unitSuffix = ' TB';
                }

            return ($suffix) ? $size . $unitSuffix : $size;
        }
    }
}
