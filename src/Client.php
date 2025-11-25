<?php

namespace likry\juguangSdk;

use likry\juguangSdk\Api\AccountApi;
use likry\juguangSdk\Api\OAuthApi;
use likry\juguangSdk\Api\ReportApi;
use likry\juguangSdk\Http\Request;
use likry\juguangSdk\Exception\JuguangSDKException;

class Client
{
    private int     $appId;
    private string  $secret;
    private ?string $accessToken;
    private Request $request;
    /** @var OAuthApi|null OAuth API实例 */
    private ?OAuthApi $oauthApi = null;
    /** @var AccountApi|null 账户API实例 */
    private ?AccountApi $accountApi = null;
    /** @var ReportApi|null 报表API实例 */
    private ?ReportApi $reportApi = null;

    public function __construct(int $appId, string $secret, ?Request $request = null)
    {
        if (empty($appId)) {
            throw JuguangSDKException::invalidConfig('App ID不能为空');
        }

        if (empty($secret)) {
            throw JuguangSDKException::invalidConfig('Secret不能为空');
        }

        $this->appId   = $appId;
        $this->secret  = $secret;
        $this->request = $request ?: new Request();
    }

    /**
     * 设置访问令牌
     *
     * @param string $accessToken
     * @return self
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * 获取访问令牌
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * 获取应用ID
     *
     * @return int
     */
    public function getAppId(): int
    {
        return $this->appId;
    }

    /**
     * 获取OAuth API实例
     *
     * @return OAuthApi
     */
    public function oauth(): OAuthApi
    {
        if ($this->oauthApi === null) {
            $this->oauthApi = new OAuthApi($this->appId, $this->secret, $this->request);
        }

        return $this->oauthApi;
    }

    /**
     * @return AccountApi
     * @throws JuguangSDKException
     */
    public function account(): AccountApi
    {
        if ($this->accessToken === null) {
            throw JuguangSDKException::invalidConfig('使用账户API需要先设置Access Token');
        }
        if ($this->accountApi === null) {
            $this->accountApi = new AccountApi($this->accessToken, $this->request);
        }
        return $this->accountApi;
    }

    public function report(): ReportApi
    {
        if ($this->accessToken === null) {
            throw JuguangSDKException::invalidConfig('使用报表API需要先设置Access Token');
        }
        if ($this->reportApi === null) {
            $this->reportApi = new ReportApi($this->accessToken, $this->request);
        }
        return $this->reportApi;
    }

    /**
     * 授权流程：获取Access Token并自动设置
     *
     * @param string $authCode 授权码
     * @return array
     * @throws JuguangSDKException
     */
    public function authenticate(string $authCode): array
    {
        $tokenData = $this->oauth()->getAccessToken($authCode);
        $this->setAccessToken($tokenData['access_token']);

        return $tokenData;
    }

    /**
     * 刷新Access Token并自动设置
     *
     * @param string $refreshToken 刷新令牌
     * @return array
     * @throws JuguangSDKException
     */
    public function refresh(string $refreshToken): array
    {
        $tokenData = $this->oauth()->refreshAccessToken($refreshToken);
        $this->setAccessToken($tokenData['access_token']);

        return $tokenData;
    }

    /**
     * 生成授权URL
     *
     * @param array  $scopes      权限范围
     * @param string $redirectUri 回调地址
     * @param string $state       自定义参数
     * @return string
     */
    public function generateAuthUrl(array $scopes, string $redirectUri, string $state = ''): string
    {
        return $this->oauth()->getAuthorizationUrl($scopes, $redirectUri, $state);
    }

    /**
     * 设置请求超时时间
     *
     * @param int $timeout 超时时间（秒）
     * @return self
     */
    public function setTimeout(int $timeout): self
    {
        $this->request->setTimeout($timeout);
        return $this;
    }

    /**
     * 设置自定义请求头
     *
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->request->setHeaders($headers);
        return $this;
    }

    /**
     * 快速创建客户端实例
     *
     * @param int         $appId
     * @param string      $secret
     * @param string|null $accessToken
     * @return self
     */
    public static function create(int $appId, string $secret, ?string $accessToken = null): self
    {
        $client = new self($appId, $secret);

        if ($accessToken !== null) {
            $client->setAccessToken($accessToken);
        }

        return $client;
    }

    /**
     * 从配置数组创建客户端实例
     *
     * @param array $config 配置数组，包含 app_id, secret, access_token（可选）
     * @return self
     * @throws JuguangSDKException
     */
    public static function fromConfig(array $config): self
    {
        if (!isset($config['app_id'], $config['secret'])) {
            throw JuguangSDKException::invalidConfig('配置数组必须包含 app_id 和 secret');
        }

        return self::create(
            (int)$config['app_id'],
            (string)$config['secret'],
            $config['access_token'] ?? null
        );
    }
}