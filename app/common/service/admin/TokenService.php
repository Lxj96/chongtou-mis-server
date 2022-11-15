<?php
/**
 * Description: Token
 * File: TokenService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\cache\admin\UserCache;
use app\common\exception\AuthException;
use Firebase\JWT\JWT;

class TokenService
{
    /**
     * Token配置
     *
     * @return array
     */
    public static function config()
    {
        return SettingService::info();
    }

    /**
     * Token生成
     *
     * @param array $user 用户信息
     *
     * @return string
     */
    public static function create($user)
    {
        $config = self::config();

        $key = $config['token_key'];                  //密钥
        $iat = time();                                //签发时间
        $nbf = time();                                //生效时间
        $exp = time() + $config['token_exp'] * 3600;  //过期时间

        $data = [
            'admin_user_id' => $user['admin_user_id'],
            'login_time' => $user['login_time'],
            'login_ip' => $user['login_ip'],
        ];

        $payload = [
            'iat' => $iat,
            'nbf' => $nbf,
            'exp' => $exp,
            'data' => $data,
        ];

        $token = JWT::encode($payload, $key);

        return $token;
    }

    /**
     * Token验证
     *
     * @param string $token token
     *
     * @throws AuthException
     */
    public static function verify($token)
    {
        try {
            $config = self::config();
            $decode = JWT::decode($token, $config['token_key'], array('HS256'));
            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            throw new AuthException('账号登录状态已过期');
        }

        $user = UserCache::get($admin_user_id);

        if (empty($user)) {
            throw new AuthException('登录已失效，请重新登录');
        }
        else {
            if ($user['is_disable']) {
                throw new AuthException('账号已被禁用,请联系管理员');
            }
            /*if ($token != $user['admin_token']) {
                throw new AuthException('账号已在另一处登录');
            }
            else {
                if ($user['is_disable']) {
                    throw new AuthException('账号已被禁用,请联系管理员');
                }
            }*/
        }
    }

    /**
     * Token用户id
     *
     * @param string $token token
     *
     * @return int admin_user_id
     */
    public static function adminUserId($token)
    {
        if (empty($token)) {
            return 0;
        }

        try {
            $config = self::config();
            $decode = JWT::decode($token, $config['token_key'], array('HS256'));
            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            $admin_user_id = 0;
        }

        return $admin_user_id;
    }
}
