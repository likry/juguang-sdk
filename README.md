# 小红书聚光平台PHP SDK (统一入口版)

小红书聚光平台官方PHP SDK，以Client为统一入口，提供OAuth认证、财务API、账户管理、报表等功能支持。

## 🎯 核心特性

### 🚀 统一入口设计
- ✅ **单一入口** - 所有功能通过 `Client` 类访问
- ✅ **链式调用** - 流畅的API调用体验
- ✅ **智能管理** - 自动Token管理和配置处理
- ✅ **简洁API** - 简化的方法命名和参数

### 📦 完整功能覆盖
- ✅ OAuth 2.0 认证授权
- ✅ 财务余额查询
- ✅ 账户信息管理
- ✅ 广告报表数据
- ✅ 完善错误处理

### ⚡ 性能优化
- ✅ 智能重试机制
- ✅ 连接复用优化
- ✅ 响应缓存支持
- ✅ 网络超时控制

## 📦 安装

```bash
composer require likry/juguang-sdk
```

## 🎮 快速开始

### 基础使用

```php
use likry\juguangSdk\Client;

// 1. 创建客户端 - 统一入口
$client = Client::create($appId, $secret);

// 2. 认证授权
$client->authenticate($authCode);

// 3. 调用API - 所有功能通过client访问
$balance = $client->finance()->queryBalance($userId, $vsellerIds);
$accountInfo = $client->account()->getAccountInfo();
$reportData = $client->report()->getDailyReport($params);
```

### 配置化使用

```php
// 使用配置数组
$client = Client::fromConfig([
    'app_id' => 123,
    'secret' => 'your-secret-here',
    'access_token' => 'existing-token',
    'timeout' => 30,
    'retry_attempts' => 3,
]);

// 动态配置
$client->setConfig([
    'debug' => true,
    'timeout' => 60,
]);
```

## 🔐 OAuth认证

### 生成授权URL

```php
$scopes = ['report_service', 'ad_query', 'account_manage'];
$redirectUri = 'http://your-domain.com/callback';

$authUrl = $client->generateAuthUrl($scopes, $redirectUri, 'custom_state');
echo "请访问: {$authUrl}";
```

### 获取Access Token

```php
// 从回调获取auth_code后
$tokenData = $client->authenticate($authCode);

echo "Access Token: " . $tokenData['access_token'];
echo "用户ID: " . $tokenData['user_id'];
echo "广告主ID: " . $tokenData['advertiser_id'];
```

### Token自动刷新

```php
// SDK会自动保存refresh_token
$client->authenticate($authCode);

// Token过期时自动刷新
$newTokenData = $client->refresh();
echo "新Token: " . $newTokenData['access_token'];
```

## 💰 财务API

### 查询账户余额

```php
$balanceData = $client->finance()->queryBalance(
    $userId,                    // 代理商主账号ID
    ['vseller1', 'vseller2']    // 子账号ID列表（最多100个）
);

// 格式化显示
$formattedBalances = $client->finance()->formatBalanceInfo($balanceData);

foreach ($formattedBalances as $balance) {
    echo "账号: " . $balance['virtual_seller_id'];
    echo "余额: ¥" . $balance['total_available_balance'];
    echo "状态: " . $balance['account_status_text'];
    echo "可转账: " . ($balance['can_transfer'] ? '是' : '否');
}
```

### 余额统计分析

```php
// 获取汇总统计
$summary = $client->finance()->getBalanceSummary($balanceData);
echo "总账户数: " . $summary['total_accounts'];
echo "活跃账户: " . $summary['active_accounts'];
echo "总余额: ¥" . $summary['total_balance'];

// 按钱包类型统计
$cashBalance = $client->finance()->getBalanceByWalletType(
    $balanceData, 
    FinanceApi::WALLET_TYPE_CASH
);
```

## 👤 账户API

### 获取账户信息

```php
$accountInfo = $client->account()->getAccountInfo();
echo "账户名称: " . $accountInfo['advertiser_name'];
echo "账户ID: " . $accountInfo['advertiser_id'];
```

### 预算管理

```php
// 获取预算信息
$budget = $client->account()->getBudget();

// 更新预算
$client->account()->updateBudget([
    'daily_budget' => 10000.00,
    'total_budget' => 300000.00,
]);
```

## 📊 报表API

### 获取日报表

```php
$dailyReport = $client->report()->getDailyReport([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'advertiser_id' => $advertiserId,
]);
```

### 账户报表

```php
$accountReport = $client->report()->getAccountReport([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'group_by' => 'date',
]);
```

### 广告报表

```php
$adReport = $client->report()->getAdReport([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'group_by' => 'ad_group',
    'advertiser_id' => $advertiserId,
]);
```

## ⚙️ 配置管理

### 快速配置

```php
// 基础配置
$client = Client::create($appId, $secret)
    ->setTimeout(60)              // 设置超时
    ->enableDebug()              // 启用调试
    ->setHeaders([               // 设置请求头
        'X-Client-Version' => '1.0.0',
    ]);

// 获取配置
echo "App ID: " . $client->getAppId();
echo "有Token: " . ($client->hasValidAccessToken() ? '是' : '否');
echo "调试模式: " . ($client->isDebugEnabled() ? '开启' : '关闭');
```

### 高级配置

```php
$config = [
    'app_id' => 123,
    'secret' => 'your-secret',
    
    // 网络配置
    'timeout' => 30,
    'retry_attempts' => 3,
    'retry_delay' => 1000,
    
    // 功能开关
    'debug' => false,
    'cache_enabled' => true,
];

$client = Client::fromConfig($config);

// 动态修改配置
$client->setConfig([
    'debug' => true,
    'timeout' => 60,
]);
```

## 🛠️ API接口一览

### 统一入口方法

| 方法 | 说明 | 返回 |
|------|------|------|
| `Client::create($appId, $secret)` | 创建客户端实例 | Client |
| `Client::fromConfig($config)` | 从配置创建客户端 | Client |
| `$client->setAccessToken($token)` | 设置访问令牌 | self |
| `$client->getAccessToken()` | 获取访问令牌 | string |
| `$client->getClientInfo()` | 获取客户端信息 | array |

### OAuth方法

| 方法 | 说明 | 参数 |
|------|------|------|
| `$client->oauth()` | 获取OAuth实例 | OAuthApi |
| `$client->generateAuthUrl($scopes, $redirectUri, $state)` | 生成授权URL | string |
| `$client->authenticate($authCode)` | 获取Access Token | array |
| `$client->refresh($refreshToken)` | 刷新Access Token | array |

### 财务方法

| 方法 | 说明 | 参数 |
|------|------|------|
| `$client->finance()` | 获取财务API实例 | FinanceApi |
| `$client->finance()->queryBalance($userId, $vsellerIds)` | 查询余额 | array |
| `$client->finance()->formatBalanceInfo($data)` | 格式化余额信息 | array |
| `$client->finance()->getBalanceSummary($data)` | 余额汇总统计 | array |

### 账户方法

| 方法 | 说明 | 参数 |
|------|------|------|
| `$client->account()` | 获取账户API实例 | AccountApi |
| `$client->account()->getAccountInfo()` | 获取账户信息 | array |
| `$client->account()->getBudget()` | 获取预算信息 | array |
| `$client->account()->updateBudget($data)` | 更新预算信息 | array |

### 报表方法

| 方法 | 说明 | 参数 |
|------|------|------|
| `$client->report()` | 获取报表API实例 | ReportApi |
| `$client->report()->getDailyReport($params)` | 获取日报表 | array |
| `$client->report()->getAccountReport($params)` | 获取账户报表 | array |
| `$client->report()->getAdReport($params)` | 获取广告报表 | array |

## 🚨 错误处理

### 统一异常处理

```php
use likry\juguangSdk\Exception\JuguangSDKException;

try {
    $result = $client->finance()->queryBalance($userId, $vsellerIds);
} catch (JuguangSDKException $e) {
    // 基本信息
    echo "错误: " . $e->getMessage();
    echo "代码: " . $e->getErrorCode();
    
    // 错误类型判断
    if ($e->isAuthError()) {
        echo "认证错误，请检查Token";
    } elseif ($e->isNetworkError()) {
        echo "网络错误，请检查连接";
    } elseif ($e->isRetryable()) {
        echo "可重试错误，建议稍后重试";
    }
    
    // 格式化错误信息
    echo $e->getFormattedMessage();
}
```

## 🔧 高级功能

### TokenManager集成

```php
// 如果你已有一个TokenManager实例
$tokenManager = TokenManager::create($appId, $secret, $accessToken);

// 转换为Client实例
$client = Client::fromTokenManager($tokenManager);
```

### 批量操作

```php
// 批量查询多个账户余额
$vsellerIds = ['id1', 'id2', 'id3', 'id4', 'id5'];
$balance = $client->finance()->queryBalance($userId, $vsellerIds);

// 批量获取报表
$reportParams = [
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'advertiser_ids' => [$adId1, $adId2, $adId3],
];
$reports = $client->report()->getAccountReport($reportParams);
```

## 📝 示例代码

### 完整业务流程

```php
use likry\juguangSdk\Client;

class JuguangService
{
    private $client;
    
    public function __construct()
    {
        // 初始化客户端
        $this->client = Client::fromConfig([
            'app_id' => getenv('JUGUANG_APP_ID'),
            'secret' => getenv('JUGUANG_SECRET'),
            'timeout' => 30,
            'retry_attempts' => 3,
        ]);
    }
    
    public function authorizeAndGetBalance(string $authCode, string $userId, array $vsellerIds)
    {
        try {
            // 1. 认证
            $tokenData = $this->client->authenticate($authCode);
            echo "认证成功，用户ID: " . $tokenData['user_id'];
            
            // 2. 查询余额
            $balanceData = $this->client->finance()->queryBalance($userId, $vsellerIds);
            
            // 3. 格式化输出
            $summary = $this->client->finance()->getBalanceSummary($balanceData);
            
            return [
                'success' => true,
                'token_info' => $tokenData,
                'balance_summary' => $summary,
            ];
            
        } catch (JuguangSDKException $e) {
            return [
                'success' => false,
                'error' => $e->getFormattedMessage(),
                'error_code' => $e->getErrorCode(),
                'retryable' => $e->isRetryable(),
            ];
        }
    }
    
    public function getDailyReports(string $startDate, string $endDate, int $advertiserId)
    {
        try {
            return $this->client->report()->getDailyReport([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'advertiser_id' => $advertiserId,
            ]);
        } catch (JuguangSDKException $e) {
            throw new Exception("获取报表失败: " . $e->getMessage());
        }
    }
}

// 使用示例
$service = new JuguangService();
$result = $service->authorizeAndGetBalance($authCode, $userId, $vsellerIds);
```

## 🧪 测试

```bash
# 运行示例
php example.php

# 或者直接测试
composer test
```

## 📁 项目结构

```
juguang-sdk/
├── src/
│   ├── Client.php              # 🎯 统一入口类
│   ├── TokenManager.php        # 🔐 Token管理器
│   ├── Api/                  # 📡 API接口
│   │   ├── OAuthApi.php     # OAuth认证
│   │   ├── FinanceApi.php   # 财务API
│   │   ├── AccountApi.php   # 账户API
│   │   └── ReportApi.php    # 报表API
│   ├── Http/                  # 🌐 HTTP请求
│   │   └── Request.php      # 请求处理
│   └── Exception/             # ⚠️ 异常处理
│       └── JuguangSDKException.php
├── tests/                    # 🧪 测试代码
├── example.php                # 📝 使用示例
├── README.md                 # 📚 文档
├── composer.json              # 📦 依赖配置
└── 聚光.md                  # 📖 API文档
```

## 🎯 设计优势

### 1. 统一入口
- **单一职责**: Client类作为所有功能的统一入口
- **链式调用**: `$client->finance()->queryBalance()`
- **一致接口**: 所有API都通过Client访问

### 2. 简洁使用
- **工厂方法**: `Client::create()` 和 `Client::fromConfig()`
- **自动管理**: Token自动获取和刷新
- **配置灵活**: 支持数组和文件配置

### 3. 功能完整
- **OAuth流程**: 完整的认证授权流程
- **财务功能**: 余额查询、统计分析
- **账户管理**: 账户信息、预算管理
- **报表数据**: 多种报表类型支持

### 4. 开发友好
- **类型安全**: 完整的类型提示
- **错误处理**: 详细的异常信息
- **调试支持**: 可选的调试模式
- **文档完善**: 详细的API文档

## 📄 许可证

MIT License

## 🤝 贡献

欢迎提交Issue和Pull Request来帮助改进这个SDK！

## 📞 联系方式

- 作者: likry
- 邮箱: 493395100@qq.com
- 项目主页: https://github.com/likry/juguang-sdk

---

**🎉 享受使用小红书聚光平台PHP SDK！通过Client统一入口，让API调用更简单！**