<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Type;

class ProductRequests
{
    /**
     * @var null|\OpenEuropa\EPoetry\Type\ProductRequest[]
     */
    protected $productRequest;

    /**
     * @param ProductRequest ...$productRequests
     *
     * @return $this
     */
    public function addProductRequest(...$productRequests): ProductRequests
    {
        foreach ($productRequests as $productRequest) {
            $this->productRequest[] = $productRequest;
        }

        return $this;
    }

    /**
     * @return null|\OpenEuropa\EPoetry\Type\ProductRequest[]
     */
    public function getProductRequest(): ?array
    {
        return $this->productRequest;
    }

    /**
     * @return bool
     */
    public function hasProductRequest(): bool
    {
        if (\is_array($this->productRequest)) {
            return !empty($this->productRequest);
        }

        return isset($this->productRequest);
    }

    /**
     * @param ProductRequest[] $productRequest
     *
     * @return $this
     */
    public function setProductRequest(array $productRequest): ProductRequests
    {
        $this->productRequest = $productRequest;

        return $this;
    }
}
