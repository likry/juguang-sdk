<?php

// 示例文件：演示如何使用小红书聚光平台SDK

require_once 'vendor/autoload.php';

use Juguang\SDK\Client;

// 初始化客户端
$appId = 123; // 替换为你的应用ID
$secret = 'your_secret'; // 替换为你的应用密钥
$client = new Client($appId, $secret);

// 1. 生成授权URL
$authUrl = $client->getAuthUrl(
    ['report_service', 'ad_query', 'ad_manage', 'account_manage'],
    'http://www.example.com/callback',
    'custom_state'
);

echo "授权URL: " . $authUrl . "\n";

// 2. 获取访问令牌（需要真实的auth_code）
/*
$authCode = 'd6a0b18531a2b9599a2c1e2361659c00'; // 替换为真实的auth_code
try {
    $response = $client->getAccessToken($authCode);
    if ($response['success']) {
        echo "获取访问令牌成功\n";
        echo "Access Token: " . $response['data']['access_token'] . "\n";
        echo "Refresh Token: " . $response['data']['refresh_token'] . "\n";
    } else {
        echo "获取访问令牌失败: " . $response['msg'] . "\n";
    }
} catch (Exception $e) {
    echo "异常: " . $e->getMessage() . "\n";
}
*/

// 3. 刷新访问令牌（需要已有的refresh_token）
/*
try {
    $client->setRefreshToken('your_refresh_token');
    $response = $client->refreshToken();
    if ($response['success']) {
        echo "刷新令牌成功\n";
        echo "New Access Token: " . $response['data']['access_token'] . "\n";
        echo "New Refresh Token: " . $response['data']['refresh_token'] . "\n";
    } else {
        echo "刷新令牌失败: " . $response['msg'] . "\n";
    }
} catch (Exception $e) {
    echo "异常: " . $e->getMessage() . "\n";
}
*/

// 4. 查询账户余额（需要有效的access_token）
/*
try {
    $client->setAccessToken('your_access_token');
    $response = $client->queryBalance('user_id', ['virtual_seller_id_1', 'virtual_seller_id_2']);
    if ($response['success']) {
        echo "查询余额成功\n";
        print_r($response['data']);
    } else {
        echo "查询余额失败: " . $response['msg'] . "\n";
    }
} catch (Exception $e) {
    echo "异常: " . $e->getMessage() . "\n";
}
*/