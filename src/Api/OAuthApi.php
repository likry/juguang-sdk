<?php

namespace Juguang\SDK\Api;

use Juguang\SDK\Http\Request;

class OAuthApi
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    protected $appId;

    /**
     * @var string
     */
    protected $secret;

    /**
     * OAuthApi constructor.
     *
     * @param Request $request
     * @param int $appId
     * @param string $secret
     */
    public function __construct(Request $request, int $appId, string $secret)
    {
        $this->request = $request;
        $this->appId = $appId;
        $this->secret = $secret;
    }

    /**
     * 获取授权URL
     *
     * @param array $scopes 权限范围
     * @param string $redirectUri 回调地址
     * @param string $state 自定义参数
     * @return string
     */
    public function getAuthUrl(array $scopes, string $redirectUri, string $state = ''): string
    {
        $params = [
            'appId' => $this->appId,
            'scope' => json_encode($scopes),
            'redirectUri' => $redirectUri,
            'state' => $state
        ];

        return 'https://ad-market.xiaohongshu.com/auth?' . http_build_query($params);
    }

    /**
     * 获取访问令牌
     *
     * @param string $authCode 授权码
     * @return array
     * @throws \Exception
     */
    public function getAccessToken(string $authCode): array
    {
        $url = '/api/open/oauth2/access_token';
        
        $params = [
            'app_id' => $this->appId,
            'secret' => $this->secret,
            'auth_code' => $authCode
        ];

        return $this->request->send('POST', $url, $params);
    }

    /**
     * 刷新访问令牌
     *
     * @param string $refreshToken 刷新令牌
     * @return array
     * @throws \Exception
     */
    public function refreshToken(string $refreshToken): array
    {
        $url = '/api/open/oauth2/refresh_token';

        $params = [
            'app_id' => $this->appId,
            'secret' => $this->secret,
            'refresh_token' => $refreshToken
        ];

        return $this->request->send('POST', $url, $params);
    }
}