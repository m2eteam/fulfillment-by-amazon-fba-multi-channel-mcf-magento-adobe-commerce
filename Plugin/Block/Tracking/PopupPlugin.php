<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Plugin\Block\Tracking;

class PopupPlugin extends \M2E\AmazonMcf\Plugin\AbstractPlugin
{
    private \M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin\McfOrderFinder $mcfOrderFinder;

    public function __construct(
        \M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin\McfOrderFinder $mcfOrderFinder,
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Helper\Magento $magentoHelper
    ) {
        parent::__construct($module, $maintenance, $magentoHelper);
        $this->mcfOrderFinder = $mcfOrderFinder;
    }

    public function aroundGetTrackingInfo(
        \Magento\Shipping\Block\Tracking\Popup $interceptor,
        \Closure $callback,
        ...$arguments
    ) {
        return $this->execute('getTrackingInfo', $interceptor, $callback, $arguments);
    }

    public function processGetTrackingInfo(
        \Magento\Shipping\Block\Tracking\Popup $interceptor,
        \Closure $callback,
        ...$arguments
    ): array {
        $result = $callback();

        $order = $this->mcfOrderFinder->tryFindByRequest($interceptor->getRequest());
        if (empty($order)) {
            return $result;
        }

        $carrierUrlByTrackingNumber = $this->getCarrierUrlByTrackingNumber($order);
        if (empty($carrierUrlByTrackingNumber)) {
            return $result;
        }

        foreach ($result as &$trackData) {
            foreach ($trackData as &$trackDataItem) {
                if (!is_array($trackDataItem)) {
                    continue;
                }

                $trackingNumber = $trackDataItem['number'] ?? null;
                $carrierTitle = $trackDataItem['title'] ?? null;

                if (
                    empty($trackingNumber)
                    || empty($carrierTitle)
                    || !isset($carrierUrlByTrackingNumber[$trackingNumber])
                ) {
                    continue;
                }

                $trackDataItem = new \M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin\TrackDataDTO(
                    $trackDataItem['title'],
                    $trackDataItem['number'],
                    $carrierUrlByTrackingNumber[$trackingNumber]
                );
            }
        }

        return $result;
    }

    private function getCarrierUrlByTrackingNumber(\M2E\AmazonMcf\Model\Order $order): array
    {
        $result = [];
        foreach ($order->getItems() as $orderItem) {
            if (
                !$orderItem->isExistsTrackingNumber()
                || !$orderItem->isExistsCarrierUrl()
            ) {
                continue;
            }

            $trackingNumber = $orderItem->getTrackingNumber();
            if (isset($result[$trackingNumber])) {
                continue;
            }

            $result[$trackingNumber] = $orderItem->getCarrierUrl();
        }

        return $result;
    }
}
