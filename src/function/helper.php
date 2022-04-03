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