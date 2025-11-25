<?php

namespace likry\juguangSdk\Exception;

use Exception;

class JuguangSDKException extends Exception
{
    protected ?string $errorCode;
    protected ?array  $responseData;

    public function __construct(string $message = "", int $code = 0, ?string $errorCode = null, ?array $responseData = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->responseData = $responseData;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public static function apiError(string $message, string $errorCode, array $responseData): self
    {
        return new self($message, 400, $errorCode, $responseData);
    }

    public static function networkError(string $message): self
    {
        return new self($message, 500);
    }

    public static function invalidConfig(string $message): self
    {
        return new self($message, 500);
    }

    public static function invalidResponse(string $message): self
    {
        return new self($message, 500);
    }
}