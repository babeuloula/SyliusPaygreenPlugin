<?php

declare(strict_types=1);

namespace Hraph\SyliusPaygreenPlugin\Client\Repository;

use Hraph\PaygreenApi\ApiException;
use Hraph\PaygreenApi\Model\Shop;
use Hraph\SyliusPaygreenPlugin\Client\PaygreenApiClientInterface;
use Hraph\SyliusPaygreenPlugin\Entity\ApiEntityInterface;
use Hraph\SyliusPaygreenPlugin\Entity\PaygreenShop;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;

class PaygreenShopRepository implements PaygreenShopRepositoryInterface
{
    private PaygreenApiClientInterface $api;

    /**
     * PaygreenShopRepository constructor.
     * @param PaygreenApiClientInterface $api
     */
    public function __construct(PaygreenApiClientInterface $api)
    {
        $this->api = $api;
    }


    /**
     * @param string $id
     * @return PaygreenShop
     * @inheritDoc
     */
    public function find($id): ?PaygreenShop
    {
        try {
            $shop = new PaygreenShop();
            $apiShop = $this->api->getShopApi()->apiIdentifiantShopShopIdGet($this->api->getUsername(), $this->api->getApiKeyWithPrefix(), $id)->getData();
            $shop->copyFromApiObject($apiShop);
            return $shop;
        }
        catch (\Exception $e) {
            throw new ApiException("Error while get shop: {$e->getMessage()}");
        }
    }

    /**
     * @return PaygreenShop[]
     * @inheritDoc
     */
    public function findAll(): array
    {
        try {
            $shops = [];
            $apiShops = $this->api->getShopsApi()->apiIdentifiantShopsGet($this->api->getUsername(), $this->api->getApiKeyWithPrefix())->getData();

            foreach ($apiShops as $apiShop){
                $shop = new PaygreenShop();
                $shop->copyFromApiObject($apiShop);
                $shops[] = $shop;
            }

            return $shops;
        }
        catch (\Exception $e) {
            throw new ApiException("Error while get shops: {$e->getMessage()}");
        }
    }

    /**
     * @inheritDoc
     */
    public function update(ApiEntityInterface $entity): void
    {
        try {
            if ($entity->isFromApiData())
                return;

            /** @var Shop $apiObject */
            $apiObject = $entity->createApiObject();
            $result = $this->api->getShopApi()->apiIdentifiantShopShopIdPut($this->api->getUsername(), $this->api->getApiKeyWithPrefix(), $entity->getId(), $apiObject);

            if (!$result->getSuccess())
                throw new ApiException("Unable to update: {$result->getMessage()}");
        }
        catch (\Exception $e) {
            throw new ApiException("Error while update shop: {$e->getMessage()}");
        }
    }

    /**
     * @inheritDoc
     */
    public function insert(ApiEntityInterface $entity): void
    {
        throw new MethodNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(ApiEntityInterface $entity): void
    {
        throw new MethodNotImplementedException();
    }
}