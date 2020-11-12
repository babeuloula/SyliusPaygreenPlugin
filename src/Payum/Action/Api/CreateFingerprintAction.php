<?php

declare(strict_types=1);

namespace Hraph\SyliusPaygreenPlugin\Payum\Action\Api;


use Hraph\PaygreenApi\ApiException;
use Hraph\PaygreenApi\Model\CardPrint;
use Hraph\PaygreenApi\Model\PayinsBuyer;
use Hraph\SyliusPaygreenPlugin\Payum\Request\Api\CreateFingerprint;
use Hraph\SyliusPaygreenPlugin\Types\PaymentDetailsKeys;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpPostRedirect;

class CreateFingerprintAction extends BaseApiAwareAction implements ActionInterface
{
    /**
     * @inheritDoc
     * @throws ApiException
     */
    public function execute($request)
    {
        $details = ArrayObject::ensureArrayObject($request->getModel());

        // Create payins object for PayGreen API from ConvertAction
        $cardPrint = new CardPrint($details->toUnsafeArrayWithoutLocal());
        $cardPrint->setBuyer(new PayinsBuyer($details['buyer']));

        try {
            $paymentRequest = $this
                ->api
                ->getPayinsCardprintApi()
                ->apiIdentifiantPayinsCardprintPost($this->api->getUsername(), $this->api->getApiKeyWithPrefix(), $cardPrint);

            if (!is_null($paymentRequest->getData()) && !is_null($paymentRequest->getData()->getId())) {
                // Save transaction id for status action
                $details[PaymentDetailsKeys::PAYGREEN_FINGERPRINT_ID] = $paymentRequest->getData()->getId();
            }
            else
                throw new ApiException("Invalid API data exception. Wrong id!");

        }
        catch (ApiException $e) {
            throw new ApiException(sprintf('Error with create fingerprint with: %s', $e->getMessage()));
        }
        catch (\Exception $e){
            throw new ApiException(sprintf('Error with create fingerprint with: %s', $e->getMessage()));
        }

        // API has returned a redirect url
        if (!is_null($paymentRequest->getData()->getUrl()))
            throw new HttpPostRedirect($paymentRequest->getData()->getUrl());

        // Otherwise use returnedUrl
        else
            throw new HttpPostRedirect($details['returned_url']);
    }

    /**
     * @inheritDoc
     */
    public function supports($request)
    {
        return
            $request instanceof CreateFingerprint &&
            $request->getModel() instanceof \ArrayAccess;
    }
}