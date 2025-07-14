<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon;

use M2E\AmazonMcf\Model\Order\StatusProcessor\PendingStatusProcessor\ShippingItemsCalculator\Item as ShippingItem;
use M2E\AmazonMcf\Model\Amazon\Connector\CreateFulfillmentOrder\Request\DestinationAddress;

class CreateOrder
{
    private Connector\CreateFulfillmentOrder $createFulfillmentOrderConnector;

    public function __construct(Connector\CreateFulfillmentOrder $createFulfillmentOrderConnector)
    {
        $this->createFulfillmentOrderConnector = $createFulfillmentOrderConnector;
    }

    /**
     * @param ShippingItem[] $shippingItems
     *
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(
        \M2E\AmazonMcf\Model\Order $order,
        \Magento\Sales\Model\Order\Address $magentoShippingAddress,
        array $shippingItems
    ): CreateOrder\Result {
        $request = $this->createRequest(
            $order,
            $magentoShippingAddress,
            $shippingItems
        );

        $response = $this->createFulfillmentOrderConnector->process(
            $order->getAccount()->getMerchantId(),
            $request
        );

        $result = new CreateOrder\Result($request->getSellerFulfillmentId());
        if (!empty($response->getMessages())) {
            $result->setMessages($response->getMessages());
        }

        return $result;
    }

    /**
     * @param ShippingItem[] $shippingItems
     */
    private function createRequest(
        \M2E\AmazonMcf\Model\Order $order,
        \Magento\Sales\Model\Order\Address $magentoShippingAddress,
        array $shippingItems
    ): Connector\CreateFulfillmentOrder\Request {
        $sellerFulfillmentId = $order->getMagentoOrderIncrementId();

        $displayableOrderId = $order->isExistsChannelExternalOrderId()
            ? $order->getChannelExternalOrderId()
            : $sellerFulfillmentId;

        $displayableOrderDate = $order->isExistsChannelPurchaseDate()
            ? $order->getChannelPurchaseDate()
            : \M2E\Core\Helper\Date::createCurrentGmt();

        $destinationAddress = $this->createRequestDestinationAddress($magentoShippingAddress);

        $request = new Connector\CreateFulfillmentOrder\Request(
            $sellerFulfillmentId,
            $displayableOrderId,
            (string)__('Thank you for your order!'),
            Connector\CreateFulfillmentOrder\Request::SHIPPING_SPEED_CATEGORY_STANDARD,
            $displayableOrderDate,
            $destinationAddress
        );

        foreach ($shippingItems as $shippingItem) {
            $request->addItem(
                new Connector\CreateFulfillmentOrder\Request\Item(
                    $shippingItem->getChannelSku(),
                    $shippingItem->getSellerFulfillmentItemId(),
                    $shippingItem->getQty(),
                )
            );
        }

        return $request;
    }

    private function createRequestDestinationAddress(
        \Magento\Sales\Model\Order\Address $magentoShippingAddress
    ): DestinationAddress {
        $middleName = !empty($magentoShippingAddress->getMiddlename())
            ? $magentoShippingAddress->getMiddlename() . ' '
            : '';
        $name = $magentoShippingAddress->getFirstname()
            . ' ' . $middleName
            . $magentoShippingAddress->getLastname();

        $streetArray = $magentoShippingAddress->getStreet();

        // Optional in magento for some european countries
        $stateOrRegion = $magentoShippingAddress->getRegionCode()
            ? $magentoShippingAddress->getRegionCode()
            : $magentoShippingAddress->getCity();

        return new DestinationAddress(
            $name,
            reset($streetArray),
            $magentoShippingAddress->getCity(),
            $magentoShippingAddress->getTelephone(),
            $stateOrRegion,
            $magentoShippingAddress->getPostcode(),
            $magentoShippingAddress->getCountryId()
        );
    }
}
