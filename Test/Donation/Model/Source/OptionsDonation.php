<?php

declare(strict_types=1);

namespace Test\Donation\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class OptionsDonation
 * @package Test\Donation\Model\Source
 */
class OptionsDonation implements OptionSourceInterface
{
    protected array $options;

    /**
     * Get options
     * @return array
     */
    public function toOptionArray(): array
    {
        $typesOfStatus = [
            0 => 'No',
            1 => 'Yes'
        ];
        $options = [];
        foreach ($typesOfStatus as $value) {
            $options[] = [
                'label' => $value,
                'value' => $value
            ];
        }
        return $options;
    }
}
