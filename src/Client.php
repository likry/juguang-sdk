<?php

namespace Juguang\SDK;

use Juguang\SDK\Api\OAuthApi;
use Juguang\SDK\Http\Request;

class Client
{
    /**
     * 应用ID
     * @var int
     */
    protected $appId;

    /**
     * 应用密钥
     * @var string
     */
    protected $secret;

    /**
     * 访问令牌
     * @var string|null
     */
    protected $accessToken;

    /**
     * 刷新令牌
     * @var string|null
     */
    protected $refreshToken;

    /**
     * HTTP请求实例
     * @var Request|null
     */
    protected $request;

    /**
     * OAuth API实例
     * @var OAuthApi|null
     */
    protected $oauthApi;

    /**
     * Client constructor.
     *
     * @param int $appId 应用ID
     * @param string $secret 应用密钥
     */
    public function __construct(int $appId, string $secret)
    {
        $this->appId = $appId;
        $this->secret = $secret;
    }

    /**
     * 获取HTTP请求实例
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        if (!$this->request) {
            $this->request = new Request();
        }
        
        return $this->request;
    }

    /**
     * 获取OAuth API实例
     *
     * @return OAuthApi
     */
    protected function getOAuthApi(): OAuthApi
    {
        if (!$this->oauthApi) {
            $this->oauthApi = new OAuthApi($this->getRequest(), $this->appId, $this->secret);
        }
        
        return $this->oauthApi;
    }

    /**
     * 设置访问令牌
     *
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * 设置刷新令牌
     *
     * @param string $refreshToken
     * @return $this
     */
    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
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
        return $this->getOAuthApi()->getAuthUrl($scopes, $redirectUri, $state);
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
        $response = $this->getOAuthApi()->getAccessToken($authCode);
        
        if ($response['success']) {
            $this->accessToken = $response['data']['access_token'];
            $this->refreshToken = $response['data']['refresh_token'];
        }

        return $response;
    }

    /**
     * 刷新访问令牌
     *
     * @return array
     * @throws \Exception
     */
    public function refreshToken(): array
    {
        if (!$this->refreshToken) {
            throw new \Exception('Refresh token is required');
        }

        $response = $this->getOAuthApi()->refreshToken($this->refreshToken);

        if ($response['success']) {
            $this->accessToken = $response['data']['access_token'];
            $this->refreshToken = $response['data']['refresh_token'];
        }

        return $response;
    }

    /**
     * 通用API调用方法
     *
     * @param string $method HTTP方法 (GET, POST, PUT, DELETE等)
     * @param string $uri API路径
     * @param array $params 请求参数
     * @param array $headers 额外的请求头
     * @return array
     * @throws \Exception
     */
    public function call(string $method, string $uri, array $params = [], array $headers = []): array
    {
        if ($this->accessToken) {
            $headers['Access-Token'] = $this->accessToken;
        }

        return $this->getRequest()->send($method, $uri, $params, $headers);
    }
}