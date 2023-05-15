<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Page\Menu;

use Aheadworks\OneStepCheckout\Block\Adminhtml\Page\Menu;
use Magento\Backend\Block\Template;

/**
 * Page Menu Item
 *
 * @method string getPath()
 * @method string getLabel()
 * @method string getResource()
 * @method string getController()
 * @method array getLinkAttributes()
 *
 * @method Item setLinkAttributes(array $linkAttributes)
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Page\Menu
 * @codeCoverageIgnore
 */
class Item extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_OneStepCheckout::page/menu/item.phtml';

    /**
     * Prepare html attributes of the link
     *
     * @return void
     */
    protected function prepareLinkAttributes(): void
    {
        $linkAttributes = is_array($this->getLinkAttributes()) ? $this->getLinkAttributes() : [];
        if (!isset($linkAttributes['href'])) {
            $linkAttributes['href'] = $this->getUrl($this->getPath());
        }
        $classes = [];
        if (isset($linkAttributes['class'])) {
            $classes = explode(' ', $linkAttributes['class']);
        }
        if ($this->isCurrent()) {
            $classes[] = 'current';
        }
        $linkAttributes['class'] = implode(' ', $classes);
        $this->setLinkAttributes($linkAttributes);
    }

    /**
     * Retrieves string presentation of link attributes
     *
     * @return string
     */
    public function serializeLinkAttributes(): string
    {
        $nameValuePairs = [];
        foreach ($this->getLinkAttributes() as $attrName => $attrValue) {
            $nameValuePairs[] = sprintf('%s="%s"', $attrName, $attrValue);
        }
        return implode(' ', $nameValuePairs);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->prepareLinkAttributes();
        if ($this->isCurrent()) {
            /** @var Menu $menu */
            $menu = $this->getParentBlock();
            if ($menu) {
                $menu->setTitle($this->getLabel());
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->getResource() && !$this->_authorization->isAllowed($this->getResource())) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Checks whether the item is current
     *
     * @return bool
     */
    private function isCurrent(): bool
    {
        return $this->getController() == $this->getRequest()->getControllerName();
    }
}
