<?php

namespace likry\juguangSdk\Http;

use likry\juguangSdk\Exception\JuguangSDKException;

class Request
{
    private const BASE_URL = 'https://adapi.xiaohongshu.com';
    private int   $timeout = 30;
    private array $headers = [];

    public function __construct(?int $timeout = null, array $headers = [])
    {
        if ($timeout !== null) {
            $this->timeout = $timeout;
        }

        $this->headers = array_merge([
            'Content-Type' => 'application/json',
        ], $headers);
    }

    public function get(string $url, array $params = [], array $headers = []): array
    {
        $queryString = !empty($params) ? '?' . http_build_query($params) : '';
        return $this->request('GET', $url . $queryString, null, $headers);
    }

    public function post(string $url, ?array $data = null, array $headers = []): array
    {
        return $this->request('POST', $url, $data, $headers);
    }

    private function request(string $method, string $url, ?array $data = null, array $headers = []): array
    {
        $fullUrl        = $this->buildUrl($url);
        $requestHeaders = array_merge($this->headers, $headers);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $this->formatHeaders($requestHeaders),
        ]);

        if ($method === 'POST' && $data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw JuguangSDKException::networkError("网络请求失败: {$error}");
        }

        if ($httpCode >= 400) {
            throw JuguangSDKException::networkError("HTTP请求失败: {$httpCode}");
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw JuguangSDKException::invalidResponse("响应格式错误: " . json_last_error_msg());
        }

        return $responseData;
    }

    private function buildUrl(string $url): string
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        return self::BASE_URL . $url;
    }

    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
}