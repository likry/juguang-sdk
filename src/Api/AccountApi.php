<?php

namespace likry\juguangSdk\Api;

use likry\juguangSdk\Http\Request;
use likry\juguangSdk\Exception\JuguangSDKException;

class AccountApi
{
    private $request;
    private $accessToken;

    public function __construct(string $accessToken, ?Request $request = null)
    {
        $this->accessToken = $accessToken;
        $this->request     = $request ?: new Request();
        $this->request->setHeaders([
            'Access-Token' => $this->accessToken,
        ]);
    }

    /**
     * @param string      $userId          代理商主账号ID
     * @param int         $page            第几页
     * @param int         $pageSize        页大小
     * @param string|null $virtualSellerId 子账户ID
     * @param int|null    $createTimeStart 子账户开户开始时间
     * @param int|null    $createTimeEnd   子账户开户结束时间
     * @return mixed
     * @throws JuguangSDKException
     */
    public function getSubAccountList(string $userId, int $page, int $pageSize, ?string $virtualSellerId = null, ?int $createTimeStart = null, ?int $createTimeEnd = null)
    {
        $params = [
            'user_id'   => $userId,
            'page'      => $page,
            'page_size' => $pageSize,
        ];
        if ($virtualSellerId) {
            $params['virtual_seller_id'] = $virtualSellerId;
        }
        if ($createTimeStart) {
            $params['create_time_start'] = $createTimeStart;
        }
        if ($createTimeEnd) {
            $params['create_time_end'] = $createTimeEnd;
        }
        $response = $this->request->post('/api/open/jg/account/sub/page', $params);
        if (!$response['success']) {
            throw JuguangSDKException::apiError(
                $response['msg'] ?? '获取子账号列表失败',
                $response['code'] ?? 'UNKNOWN_ERROR',
                $response
            );
        }
        return $response['data'];
    }

    /**
     * 获取账户日预算余额接口
     * @param string $advertiserId 品牌方ID
     * @return mixed
     * @throws JuguangSDKException
     */
    public function getAccountBudgetInfo(string $advertiserId)
    {
        $params   = [
            'advertiser_id' => $advertiserId,
        ];
        $response = $this->request->post('/api/open/jg/account/budget/info', $params);
        if (!$response['success']) {
            throw JuguangSDKException::apiError(
                $response['msg'] ?? '获取账户日预算余额失败',
                $response['code'] ?? 'UNKNOWN_ERROR',
                $response
            );
        }
        return $response['data'];
    }

}