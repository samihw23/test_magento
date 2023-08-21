<?php

declare(strict_types=1);

namespace Test\Donation\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Test\Donation\Block\Cart\Donation;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * class to handle "Add product to cart" or "Remove product from cart" request
 *
 * class Manage
 * @package Test\Donation\Controller\Cart
 */
class Manage extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    const CART_INDEX_URL = 'checkout/cart/';
    private ProductRepositoryInterface $productRepository;
    private Donation $donation;
    private CartRepositoryInterface $cartRepository;
    private Session $checkoutSession;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Donation $donation
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Donation $donation,
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->donation = $donation;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
    }
    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $product = $this->productRepository->get($this->donation->getProductSku());
            $quote = $this->checkoutSession->getQuote();
            $price = (float)$this->getRequest()->getParam('donation-amount');
            if ($this->getRequest()->getParam('donation-amount')) {
                // add donation product to quote and se custom price
                $this->addProductDonationToCart($quote, $product, $price);
            }elseif ($this->getRequest()->getParam('cancel-donation') === '1') {
                // delete donation product from cart
                $this->removeProductDonationFromCart($quote);
            }
            $this->cartRepository->save($quote);
            return $this->_redirect(self::CART_INDEX_URL);

        } catch (NoSuchEntityException $e) {
            return $this->_redirect(self::CART_INDEX_URL);
        }

    }

    /**
     * add product donation to cart
     * @param Quote $quote
     * @param Product $product
     * @param float $price
     * @return void
     * @throws LocalizedException
     */
    protected function addProductDonationToCart(Quote $quote, Product $product, float $price): void
    {
        if (!empty($quote && $price)) {
            $quote->addProduct($product, 1);
            $quote->setDonationAmount($price);
            $item = $quote->getItemByProduct($product);
            if ($item) {
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
            }

        }
    }

    /**
     * remove product donation from cart
     * @param Quote $quote
     * @return void
     */
    protected function removeProductDonationFromCart(Quote $quote): void
    {
        $allItems = $quote->getAllVisibleItems();
        foreach ($allItems as $item) {
            if ($this->donation->getProductSku() == $item->getSku()) {
                $itemId = $item->getItemId();
                $quote->removeItem($itemId);
            }
        }
    }
}
