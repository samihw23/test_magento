<?php
declare(strict_types=1);


namespace Test\Donation\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * class ProductsResolver
 * @package Test\Donation\Model\Resolver
 */
class ProductsResolver implements ResolverInterface
{
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
     public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
     {
        if (!isset($args['manufacturer'])) {
            throw new GraphQlInputException(__('Manufacturer should be specified'));
        }
        return $this->getBrandsData($args['manufacturer']);
     }

    /**
     * @param string $manufacturer
     * @return array
     */
    protected function getBrandsData(string $manufacturer): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('manufacturer', $manufacturer)->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();
        $productRecord = [];
        foreach ($products as $key => $product) {
            $productRecord[$key]['brand'] = $product->getBrand();
        }
        return $productRecord;
    }
}
