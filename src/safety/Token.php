<?php
/**
 * TOKEN令牌类库
 *
 * @Author Raj Luo
 */

namespace Lyd3e\Lbcp\Safety;

use Exception;

/**
 * composer require firebase/php-jwt 6.1.1
 */
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Token
{
    /**
     * token令牌签发的key
     *
     * @var string
     */
    private static $key = 'LYD3E_S_SEAL_OR_TALLY';

    /**
     * 签发token
     *
     * @param array $data 自定义信息数组
     * @param string $key 签发token的key
     * @param int $exp token的有效期，单位秒
     * @param string $alg 签名算法，支持算法有'ES384'、'ES256'、'HS256'、'HS384'、'HS512'、'RS256'、'RS384'、'RS512'
     * @return string 签发成功返回token字符串，失败返回空字符串
     */
    public static function createToken($data = [], $key = '', $exp = 1800, $alg= 'HS256'): string
    {
        //处理token的签发参数
        $key = empty($key) ? self::$key : $key;

        $time = time();

        $token = [
            'iat' => $time, //签发时间
            'nbf' => $time, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time + $exp, //过期时间：半个钟不访问刷新通行证就必须重新登录
            'data' => $data
        ];

        //签发token
        try {
            $token = JWT::encode($token, $key, $alg);

        } catch (Exception $e) {
            return '';
        }

        return $token;
    }

    /**
     * 验证token
     *
     * @param string $token token字符串
     * @param string $key 签发token的key
     * @param array $alg 签名算法，支持算法有'ES384'、'ES256'、'HS256'、'HS384'、'HS512'、'RS256'、'RS384'、'RS512'
     * @return false|int|object 验证成功返回token解密的数据数组，过期返回-1，无效返回0
     */
    public static function verifyToken($token = '', $key = '', $alg= 'HS256')
    {
        //判断token是否为空
        if (empty($token)) {
            return false;
        }

        //判断key是否为空
        $key = empty($key) ? self::$key : $key;

        //验证token
        try {
            JWT::$leeway = 60; //当前时间减去60，把时间留点余地

            return JWT::decode($token, new Key($key, $alg)); //HS256方式，与签发（默认HS256）的时候对应

        } catch (ExpiredException $e) {
            return -1; //token过期

        } catch (Exception $e) {
            return 0; //token无效
        }
    }
}
