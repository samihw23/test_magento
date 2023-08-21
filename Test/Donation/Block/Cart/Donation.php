<?php

declare(strict_types=1);

namespace Test\Donation\Block\Cart;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Model\Session;

/**
 * Class Donation
 * @package Test\Donation\Block\Cart
 */
class Donation extends Template
{
    const PATH_CONFIG_ACTIVE = 'test_donation/general/enabled';
    const PATH_CONFIG_PRODUCT_SKU = 'test_donation/general/donation_sku';
    const PATH_CONFIG_TITLE = 'test_donation/general/title';
    const PATH_CONFIG_DESCRIPTION = 'test_donation/general/description';
    const PATH_CONFIG_AMOUNT = 'test_donation/general/amount';

    private ProductRepositoryInterface $productRepository;
    private Image $imageHelper;
    private Session $checkoutSession;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Image $imageHelper
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Image $imageHelper,
        Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->_scopeConfig->getValue(
            self::PATH_CONFIG_ACTIVE,
            ScopeInterface::SCOPE_STORE
        ) && !empty($this->_scopeConfig->getValue(
            self::PATH_CONFIG_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * get product sku from config
     * @return string
     */
    public function getProductSku(): string
    {
        return $this->_scopeConfig->getValue(
            self::PATH_CONFIG_PRODUCT_SKU,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get Product Image url
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductImageUrl(): string
    {
        $product = $this->productRepository->get($this->getProductSku());
        return  $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();

    }
    /**
     * get title from config
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_scopeConfig->getValue(
            self::PATH_CONFIG_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * get description from config
     * @return string
     */
    public function getDescription(): string
    {
        return $this->_scopeConfig->getValue(
            self::PATH_CONFIG_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * get amount from config
     * @return float
     */
    public function getAmount(): float
    {
        return (float)$this->_scopeConfig->getValue(
            self::PATH_CONFIG_AMOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check if donation product is already in cart
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function hasDonated(): bool
    {
        $quote = $this->checkoutSession->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($this->getProductSku() == $item->getSku() && !empty($quote->getDonationAmount())) {
                return true;
            }
        }
        return false;
    }
}
