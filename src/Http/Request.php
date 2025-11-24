<?php

namespace Juguang\SDK\Http;

class Request
{
    /**
     * API基础URL
     * @var string
     */
    protected $baseUrl = 'https://adapi.xiaohongshu.com';

    /**
     * 发送HTTP请求
     *
     * @param string $method 请求方法
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param array $headers 请求头
     * @return array
     * @throws \Exception
     */
    public function send(string $method, string $url, array $params = [], array $headers = []): array
    {
        // 如果URL不是绝对路径，则加上基础URL
        if (strpos($url, 'http') !== 0) {
            $url = $this->baseUrl . $url;
        }

        $ch = curl_init();

        // 设置默认头部
        $defaultHeaders = [
            'Content-Type: application/json',
        ];
        
        $headers = array_merge($defaultHeaders, $headers);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('Curl Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg());
        }

        return $result ?: [];
    }
}