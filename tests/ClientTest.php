<?php

namespace likry\juguangSdk\Tests;

use PHPUnit\Framework\TestCase;
use likry\juguangSdk\Client;
use likry\juguangSdk\Exception\JuguangSDKException;

class ClientTest extends TestCase
{
    private $appId = 123;
    private $secret = 'test-secret';
    private $accessToken = 'test-access-token';

    public function testCreateClient()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $this->assertInstanceOf(Client::class, $client);
        $this->assertNull($client->getAccessToken());
    }

    public function testCreateClientWithAccessToken()
    {
        $client = Client::create($this->appId, $this->secret, $this->accessToken);
        
        $this->assertEquals($this->accessToken, $client->getAccessToken());
    }

    public function testFromConfig()
    {
        $config = [
            'app_id' => $this->appId,
            'secret' => $this->secret,
            'access_token' => $this->accessToken
        ];
        
        $client = Client::fromConfig($config);
        
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($this->accessToken, $client->getAccessToken());
    }

    public function testFromConfigWithoutRequiredFields()
    {
        $this->expectException(JuguangSDKException::class);
        $this->expectExceptionMessage('配置数组必须包含 app_id 和 secret');
        
        Client::fromConfig(['app_id' => $this->appId]);
    }

    public function testSetAndGetAccessToken()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $this->assertNull($client->getAccessToken());
        
        $client->setAccessToken($this->accessToken);
        $this->assertEquals($this->accessToken, $client->getAccessToken());
    }

    public function testCreateClientWithInvalidConfig()
    {
        $this->expectException(JuguangSDKException::class);
        $this->expectExceptionMessage('App ID不能为空');
        
        new Client(0, $this->secret);
    }

    public function testCreateClientWithEmptySecret()
    {
        $this->expectException(JuguangSDKException::class);
        $this->expectExceptionMessage('Secret不能为空');
        
        new Client($this->appId, '');
    }

    public function testGenerateAuthUrl()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $scopes = ['report_service', 'ad_query'];
        $redirectUri = 'http://example.com/callback';
        $state = 'test-state';
        
        $authUrl = $client->generateAuthUrl($scopes, $redirectUri, $state);
        
        $this->assertStringContains('appId=' . $this->appId, $authUrl);
        $this->assertStringContains('redirectUri=' . urlencode($redirectUri), $authUrl);
        $this->assertStringContains('state=' . urlencode($state), $authUrl);
        $this->assertStringContains('scope=' . urlencode(json_encode($scopes)), $authUrl);
    }

    public function testOAuthApiInstance()
    {
        $client = Client::create($this->appId, $this->secret);
        $oauthApi = $client->oauth();
        
        $this->assertInstanceOf(\likry\juguangSdk\Api\OAuthApi::class, $oauthApi);
    }

    public function testFinanceApiRequiresAccessToken()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $this->expectException(JuguangSDKException::class);
        $this->expectExceptionMessage('使用财务API需要先设置Access Token');
        
        $client->finance();
    }

    public function testFinanceApiWithAccessToken()
    {
        $client = Client::create($this->appId, $this->secret, $this->accessToken);
        $financeApi = $client->finance();
        
        $this->assertInstanceOf(\likry\juguangSdk\Api\FinanceApi::class, $financeApi);
    }

    public function testSetTimeout()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $result = $client->setTimeout(60);
        
        $this->assertInstanceOf(Client::class, $result);
    }

    public function testSetHeaders()
    {
        $client = Client::create($this->appId, $this->secret);
        
        $headers = ['Custom-Header' => 'value'];
        $result = $client->setHeaders($headers);
        
        $this->assertInstanceOf(Client::class, $result);
    }
}