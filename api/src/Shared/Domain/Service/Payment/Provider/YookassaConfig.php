<?php

namespace App\Shared\Domain\Service\Payment\Provider;

use Webmozart\Assert\Assert;

class YookassaConfig
{
    public function __construct(
        private string $name,
        private string $shopId,
        private string $secretKey,
        private string $returnUrl,
    ) {
        Assert::notEmpty($name);
        Assert::notEmpty($shopId);
        Assert::notEmpty($secretKey);
        Assert::notEmpty($returnUrl);
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getShopId(): string
    {
        return $this->shopId;
    }
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }
}
