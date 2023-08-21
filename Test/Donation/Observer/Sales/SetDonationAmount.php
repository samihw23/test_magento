<?php

declare(strict_types=1);

namespace Test\Donation\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * class to set donation amount after order save
 *
 * Class SetDonationAmount
 * @package Test\Donation\Observer\Sales
 */
class SetDonationAmount implements ObserverInterface
{
    private CartRepositoryInterface $quoteRepository;
    /**
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(CartRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }
    /**
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $order->setDonationAmount((float)$quote->getDonationAmount());
        $order->save();
    }
}
