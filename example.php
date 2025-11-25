<?php

require_once __DIR__ . '/vendor/autoload.php';

use likry\juguangSdk\Client;
use likry\juguangSdk\Exception\JuguangSDKException;

// 配置信息
$appId = 123; // 替换为你的应用ID
$secret = 'your-secret-here'; // 替换为你的Secret
$redirectUri = 'http://your-domain.com/callback'; // 替换为你的回调地址

try {
    echo "=== 小红书聚光平台PHP SDK 统一入口示例 ===\n\n";

    // 1. 创建客户端实例 - 统一入口
    echo "1. 创建客户端实例\n";
    $client = Client::create($appId, $secret);
    
    // 或者使用配置数组
    // $client = Client::fromConfig([
    //     'app_id' => $appId,
    //     'secret' => $secret,
    //     'access_token' => 'your-existing-token',
    // ]);
    
    echo "✅ 客户端创建完成\n";
    echo "✅ App ID: " . $client->getAppId() . "\n\n";

    // 2. 生成授权URL
    echo "2. 生成授权URL\n";
    $scopes = ['report_service', 'ad_query', 'account_manage'];
    $authUrl = $client->generateAuthUrl($scopes, $redirectUri, 'custom_state_123');
    echo "授权URL: " . substr($authUrl, 0, 80) . "...\n";
    echo "✅ 授权URL生成完成\n\n";

    // 3. 认证流程
    echo "3. 认证流程\n";
    // 注意：这里使用的是模拟的授权码，实际使用时需要从回调URL中获取真实的auth_code
    $mockAuthCode = 'mock_auth_code_here';
    echo "注意：请使用真实的授权码进行认证\n";
    
    // $tokenData = $client->authenticate($mockAuthCode);
    // echo "✅ 认证成功\n";
    // echo "Access Token: " . substr($tokenData['access_token'], 0, 10) . "...\n";
    // echo "用户ID: " . $tokenData['user_id'] . "\n";
    // echo "广告主ID: " . $tokenData['advertiser_id'] . "\n";
    
    // 如果已经有Access Token，可以直接设置
    $existingAccessToken = 'your_existing_access_token'; // 替换为实际的Access Token
    $client->setAccessToken($existingAccessToken);
    echo "✅ 已设置Access Token\n\n";

    // 4. 查询余额 - 通过Client统一访问
    echo "4. 查询账户余额\n";
    try {
        $userId = 'your_user_id'; // 替换为实际的代理商主账号ID
        $virtualSellerIdList = [
            'virtual_seller_id_1', // 替换为实际的子账号ID
            'virtual_seller_id_2', // 可以添加更多子账号ID
        ];

        echo "注意：请使用真实的用户ID和虚拟卖家ID进行查询\n";
        // $balanceData = $client->finance()->queryBalance($userId, $virtualSellerIdList);
        
        // 为了演示，使用模拟数据
        $balanceData = [
            'wallet_balance_list' => [
                [
                    'virtual_seller_id' => 'demo_seller_1',
                    'total_available_balance' => '11000.11',
                    'total_frozen_balance' => '0.00',
                    'total_balance' => '11000.11',
                    'account_status' => 1,
                    'balance_list' => [
                        [
                            'wallet_type' => 0,
                            'available_balance' => '11000.11',
                            'frozen_balance' => '0.00',
                            'total_balance' => '11000.11',
                        ],
                        [
                            'wallet_type' => 1,
                            'available_balance' => '1000.00',
                            'frozen_balance' => '0.00',
                            'total_balance' => '1000.00',
                        ],
                    ],
                ],
            ],
        ];
        
        // 格式化余额信息
        $formattedBalances = $client->finance()->formatBalanceInfo($balanceData);
        
        echo "📊 余额查询结果:\n";
        foreach ($formattedBalances as $balance) {
            echo "┌─────────────────────────────────\n";
            echo "│ 虚拟卖家ID: " . $balance['virtual_seller_id'] . "\n";
            echo "│ 账户状态: " . $balance['account_status_text'] . "\n";
            echo "│ 总可用余额: ¥" . $balance['total_available_balance'] . "\n";
            echo "│ 总冻结余额: ¥" . $balance['total_frozen_balance'] . "\n";
            echo "│ 总余额: ¥" . $balance['total_balance'] . "\n";
            echo "│ 可转账: " . ($balance['can_transfer'] ? '是' : '否') . "\n";
            echo "├─────────────────────────────────\n";
            echo "│ 余额明细:\n";
            foreach ($balance['balance_details'] as $detail) {
                echo "│   • " . $detail['wallet_type_text'] . ": ¥" . 
                     $detail['available_balance'] . " (可用) / ¥" . 
                     $detail['frozen_balance'] . " (冻结)\n";
            }
            echo "└─────────────────────────────────\n\n";
        }

        // 生成余额汇总
        $summary = $client->finance()->getBalanceSummary($balanceData);
        echo "📈 余额汇总:\n";
        echo "• 总账户数: " . $summary['total_accounts'] . "\n";
        echo "• 活跃账户: " . $summary['active_accounts'] . "\n";
        echo "• 冻结账户: " . $summary['frozen_accounts'] . "\n";
        echo "• 总余额: ¥" . $summary['total_balance'] . "\n";
        echo "• 总可用余额: ¥" . $summary['total_available'] . "\n";
        echo "• 总冻结余额: ¥" . $summary['total_frozen'] . "\n\n";

    } catch (JuguangSDKException $e) {
        echo "❌ 查询余额失败: " . $e->getFormattedMessage() . "\n";
        if ($e->getErrorCode()) {
            echo "错误代码: " . $e->getErrorCode() . "\n";
        }
        echo "是否可重试: " . ($e->isRetryable() ? '是' : '否') . "\n";
        if ($e->isAuthError()) {
            echo "提示：这是认证错误，请检查Token是否有效\n";
        }
    }

    // 5. Token刷新
    echo "5. Token刷新演示\n";
    echo "注意：请使用真实的refresh_token进行刷新\n";
    // $refreshToken = 'your_refresh_token';
    // $newTokenData = $client->refresh($refreshToken);
    // echo "✅ Token刷新成功\n";
    // echo "新Access Token: " . substr($newTokenData['access_token'], 0, 10) . "...\n\n";

    // 6. 客户端信息
    echo "6. 客户端信息\n";
    $clientInfo = $client->getClientInfo();
    echo "• App ID: " . $clientInfo['app_id'] . "\n";
    echo "• 有Access Token: " . ($clientInfo['has_access_token'] ? '是' : '否') . "\n";
    echo "• 有Refresh Token: " . ($clientInfo['has_refresh_token'] ? '是' : '否') . "\n";
    echo "• 调试模式: " . ($clientInfo['debug_enabled'] ? '开启' : '关闭') . "\n";
    echo "• 超时时间: " . $clientInfo['timeout'] . " 秒\n";
    echo "• 重试次数: " . $clientInfo['retry_attempts'] . "\n\n";

    // 7. 可用权限范围
    echo "7. 可用权限范围:\n";
    $availableScopes = $client->oauth()->getAvailableScopes();
    foreach ($availableScopes as $scope => $description) {
        echo "• {$scope}: {$description}\n";
    }

    echo "\n=== 示例完成 ===\n";
    echo "💡 统一入口优势:\n";
    echo "1. 一个Client类搞定所有功能\n";
    echo "2. 方法调用链式: \$client->finance()->queryBalance()\n";
    echo "3. 自动Token管理: \$client->authenticate() -> \$client->refresh()\n";
    echo "4. 统一配置管理: \$client->setConfig()\n";
    echo "5. 简化的创建方式: Client::create() 或 Client::fromConfig()\n";

} catch (JuguangSDKException $e) {
    echo "❌ SDK错误: " . $e->getFormattedMessage() . "\n";
    if ($e->getErrorCode()) {
        echo "错误代码: " . $e->getErrorCode() . "\n";
    }
    if ($e->getResponseData()) {
        echo "响应数据: " . json_encode($e->getResponseData(), JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (Exception $e) {
    echo "❌ 系统错误: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . ":" . $e->getLine() . "\n";
}