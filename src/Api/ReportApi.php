<?php

namespace likry\juguangSdk\Api;

use likry\juguangSdk\Http\Request;
use likry\juguangSdk\Exception\JuguangSDKException;

class ReportApi
{
    private Request $request;
    private string  $accessToken;

    public function __construct(string $accessToken, ?Request $request = null)
    {
        $this->accessToken = $accessToken;
        $this->request     = $request ?: new Request();
        $this->request->setHeaders([
            'Access-Token' => $this->accessToken,
        ]);
    }

    /**
     * @param string $advertiserId   广告主ID
     * @param string $startDate      开始时间，格式 yyyy-MM-dd    示例：2023-09-20
     * @param string $endDate        结束时间，格式 yyyy-MM-dd    示例：2023-09-21
     * @param bool   $needHourlyData 否    是否拉取小时数据    只支持拉取今日数据
     * @return mixed
     * @throws JuguangSDKException
     */
    public function accountReport(string $advertiserId, string $startDate, string $endDate, bool $needHourlyData = false)
    {
        $params   = [
            'advertiser_id'    => $advertiserId,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'need_hourly_data' => $needHourlyData,
        ];
        $response = $this->request->post('/api/open/jg/data/report/realtime/account', $params);
        if (!$response['success']) {
            throw JuguangSDKException::apiError(
                $response['msg'] ?? '获取子账号列表失败',
                $response['code'] ?? 'UNKNOWN_ERROR',
                $response
            );
        }
        return $response['data'];
    }

}