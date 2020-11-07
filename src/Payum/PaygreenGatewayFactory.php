<?php

declare(strict_types=1);

namespace Hraph\SyliusPaygreenPlugin\Payum;

use Hraph\SyliusPaygreenPlugin\Client\PaygreenApiClient;
use Hraph\SyliusPaygreenPlugin\Client\PaygreenApiClientInterface;
use Hraph\SyliusPaygreenPlugin\Payum\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class PaygreenGatewayFactory extends GatewayFactory
{
    public const FACTORY_NAME = 'paygreen';

    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::FACTORY_NAME,
            'payum.factory_title' => 'PayGreen',
            'payum.action.status' => new StatusAction(),
        ]);

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'username' => null,
                'api_key' => null,
                'use_sandbox_api' => false,
                'payment_type' => "CB"
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'username',
                'api_key',
            ];

            // Set api config to client object
            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $paygreenApiClient = new PaygreenApiClient();

                $paygreenApiClient->setUsername($config['username']);
                $paygreenApiClient->setApiKey($config['api_key']);
                if ($config['use_sandbox_api'])
                    $paygreenApiClient->useSandboxApi($config['use_sandbox_api']);
                $paygreenApiClient->setPaymentType($config['payment_type']);

                return $paygreenApiClient;
            };
        }
    }

}
