<?php

declare(strict_types=1);

namespace Hraph\SyliusPaygreenPlugin\Client\Repository;


use Hraph\PaygreenApi\ApiException;
use Hraph\SyliusPaygreenPlugin\Entity\ApiEntityInterface;
use Hraph\SyliusPaygreenPlugin\Exception\PaygreenException;

interface PaygreenApiRepositoryInterface
{
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return ApiEntityInterface|null The object.
     * @throws PaygreenException
     */
    public function find($id): ?ApiEntityInterface;

    /**
     * Finds all objects in the repository.
     *
     * @return ApiEntityInterface[] The objects.
     * @throws PaygreenException
     */
    public function findAll(): array;

    /**
     * Update entity
     * @param ApiEntityInterface $entity
     * @throws PaygreenException
     */
    public function update(ApiEntityInterface $entity): void;

    /**
     * Insert entity
     * @param ApiEntityInterface $entity
     * @throws PaygreenException
     */
    public function insert(ApiEntityInterface $entity): void;

    /**
     * Delete entity
     * @param ApiEntityInterface $entity
     * @throws PaygreenException
     */
    public function delete(ApiEntityInterface $entity): void;
}