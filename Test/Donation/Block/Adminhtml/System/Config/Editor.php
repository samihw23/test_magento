<?php

declare(strict_types=1);

namespace Test\Donation\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConf;

/**
 * Class Editor
 * @package Test\Donation\Block\Adminhtml\System\Config
 */
class Editor extends Field
{
    private WysiwygConf $wysiwygConfig;

    /**
     * Editor constructor.
     * @param Context $context
     * @param WysiwygConf $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        WysiwygConf $wysiwygConfig,
        array $data = []
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function _getElementHtml(AbstractElement $element): string
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    }
}
