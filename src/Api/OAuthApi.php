<?php

namespace likry\juguangSdk\Api;

use likry\juguangSdk\Http\Request;
use likry\juguangSdk\Exception\JuguangSDKException;

class OAuthApi
{
    private Request $request;
    private int     $appId;
    private string  $secret;

    public function __construct(int $appId, string $secret, ?Request $request = null)
    {
        $this->appId   = $appId;
        $this->secret  = $secret;
        $this->request = $request ?: new Request();
    }

    /**
     * 生成授权URL
     *
     * @param array  $scopes      权限范围，如 ['report_service', 'ad_query']
     * @param string $redirectUri 回调地址
     * @param string $state       自定义参数
     * @return string
     */
    public function getAuthorizationUrl(array $scopes, string $redirectUri, string $state = ''): string
    {
        $params = [
            'appId'       => $this->appId,
            'scope'       => json_encode($scopes),
            'redirectUri' => $redirectUri,
            'state'       => $state
        ];

        return 'https://ad-market.xiaohongshu.com/auth?' . http_build_query($params);
    }

    /**
     * 获取Access Token
     *
     * @param string $authCode 授权码
     * @return array
     * @throws JuguangSDKException
     */
    public function getAccessToken(string $authCode): array
    {
        $data = [
            'app_id'    => $this->appId,
            'secret'    => $this->secret,
            'auth_code' => $authCode,
        ];

        $response = $this->request->post('/api/open/oauth2/access_token', $data);

        if (!$response['success']) {
            throw JuguangSDKException::apiError(
                $response['msg'] ?? '获取Access Token失败',
                $response['code'] ?? 'UNKNOWN_ERROR',
                $response
            );
        }

        return $response['data'];
    }

    /**
     * 刷新Access Token
     *
     * @param string $refreshToken 刷新令牌
     * @return array
     * @throws JuguangSDKException
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $data = [
            'app_id'        => $this->appId,
            'secret'        => $this->secret,
            'refresh_token' => $refreshToken,
        ];

        $response = $this->request->post('/api/open/oauth2/refresh_token', $data);

        if (!$response['success']) {
            throw JuguangSDKException::apiError(
                $response['msg'] ?? '刷新Access Token失败',
                $response['code'] ?? 'UNKNOWN_ERROR',
                $response
            );
        }

        return $response['data'];
    }

    /**
     * 获取可用的权限范围
     *
     * @return array
     */
    public function getAvailableScopes(): array
    {
        return [
            'report_service' => '获取账户报表信息',
            'ad_query'       => '获取推广计划、推广单元、推广创意信息',
            'ad_manage'      => '创建&修改推广计划、推广单元、推广创意',
            'account_manage' => '账户管理',
        ];
    }

    /**
     * 验证权限范围是否有效
     *
     * @param array $scopes
     * @return bool
     */
    public function validateScopes(array $scopes): bool
    {
        $availableScopes = array_keys($this->getAvailableScopes());
        return !array_diff($scopes, $availableScopes);
    }
}