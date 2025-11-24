# 小红书聚光平台 PHP SDK

[![Latest Stable Version](https://poser.pugx.org/your-vendor/juguang-sdk/v)](https://packagist.org/packages/your-vendor/juguang-sdk)
[![Total Downloads](https://poser.pugx.org/your-vendor/juguang-sdk/downloads)](https://packagist.org/packages/your-vendor/juguang-sdk)
[![License](https://poser.pugx.org/your-vendor/juguang-sdk/license)](https://packagist.org/packages/your-vendor/juguang-sdk)

这是一个基于小红书聚光平台API开发的PHP SDK，方便开发者快速集成小红书聚光平台的功能。

## 功能特性

- 简单易用的OAuth认证流程
- 访问令牌自动刷新机制
- 通用API调用方法，支持所有平台接口
- 按需加载，性能优化
- 符合PSR标准的代码风格

## 环境要求

- PHP >= 7.4
- ext-curl
- ext-json

## 安装

使用 Composer 安装：

```bash
composer require your-vendor/juguang-sdk
```

## 快速开始

### 初始化客户端

```php
use Juguang\SDK\Client;

$client = new Client(APP_ID, SECRET);
```

### 获取授权URL

```php
$authUrl = $client->getAuthUrl(
    ['report_service', 'ad_query', 'ad_manage', 'account_manage'],
    'http://your-domain.com/callback',
    'state'
);

echo $authUrl;
```

### 获取访问令牌

```php
$response = $client->getAccessToken('AUTH_CODE');
if ($response['success']) {
    echo "Access Token: " . $response['data']['access_token'];
    echo "Refresh Token: " . $response['data']['refresh_token'];
}
```

### 刷新访问令牌

```php
$response = $client->refreshToken();
if ($response['success']) {
    echo "New Access Token: " . $response['data']['access_token'];
    echo "New Refresh Token: " . $response['data']['refresh_token'];
}
```

### 通用API调用方法

对于其他接口，SDK提供了一个通用的调用方法：

```php
// 查询账户余额示例
$client->setAccessToken('YOUR_ACCESS_TOKEN');
$response = $client->call(
    'POST', 
    '/api/open/finance/balance/query', 
    [
        'user_id' => 'USER_ID',
        'virtual_seller_id_list' => ['VIRTUAL_SELLER_ID_1', 'VIRTUAL_SELLER_ID_2']
    ]
);

if ($response['success']) {
    print_r($response['data']);
}
```

## 架构说明

SDK采用了分层架构设计，将HTTP请求处理和API接口分离：

1. `Juguang\SDK\Http\Request` - 处理底层HTTP请求
2. `Juguang\SDK\Api\OAuthApi` - 处理OAuth认证相关接口
3. `Juguang\SDK\Client` - 统一入口，协调各个组件工作并提供通用调用方法

## API参考

### Client类

#### 构造函数

```php
new Client(int $appId, string $secret)
```

#### 方法

- `setAccessToken(string $accessToken): self` - 设置访问令牌
- `setRefreshToken(string $refreshToken): self` - 设置刷新令牌
- `getAuthUrl(array $scopes, string $redirectUri, string $state = ''): string` - 获取授权URL
- `getAccessToken(string $authCode): array` - 获取访问令牌
- `refreshToken(): array` - 刷新访问令牌
- `call(string $method, string $uri, array $params = [], array $headers = []): array` - 通用API调用方法

## 许可证

MIT