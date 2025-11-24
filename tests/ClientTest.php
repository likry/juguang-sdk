<?php

namespace Juguang\SDK\Tests;

use Juguang\SDK\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $appId = 123;
    private $secret = 'test_secret';
    
    public function testConstructor()
    {
        $client = new Client($this->appId, $this->secret);
        
        $this->assertInstanceOf(Client::class, $client);
    }
    
    public function testGetAuthUrl()
    {
        $client = new Client($this->appId, $this->secret);
        $scopes = ['report_service', 'ad_query'];
        $redirectUri = 'http://test.com/callback';
        $state = 'test_state';
        
        $url = $client->getAuthUrl($scopes, $redirectUri, $state);
        
        $this->assertStringContainsString('ad-market.xiaohongshu.com/auth', $url);
        $this->assertStringContainsString('appId=' . $this->appId, $url);
        $this->assertStringContainsString('redirectUri=' . urlencode($redirectUri), $url);
        $this->assertStringContainsString('state=' . $state, $url);
    }
    
    public function testSetAccessToken()
    {
        $client = new Client($this->appId, $this->secret);
        $token = 'test_access_token';
        
        $result = $client->setAccessToken($token);
        
        $this->assertInstanceOf(Client::class, $result);
    }
    
    public function testSetRefreshToken()
    {
        $client = new Client($this->appId, $this->secret);
        $token = 'test_refresh_token';
        
        $result = $client->setRefreshToken($token);
        
        $this->assertInstanceOf(Client::class, $result);
    }
}