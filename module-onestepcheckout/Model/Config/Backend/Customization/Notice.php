<?php
namespace Aheadworks\OneStepCheckout\Model\Config\Backend\Customization;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Notice
 *
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\Customization
 */
class Notice
{
    const ENTERPRISE = 'Enterprise';

    /**
     * @var bool
     */
    private $isDisplayed = false;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Notice constructor.
     * @param ManagerInterface $messageManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ManagerInterface $messageManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->messageManager = $messageManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        if (!$this->isDisplayed) {
            if ($this->productMetadata->getEdition() == self::ENTERPRISE) {
                $message = __(
                    'Please make sure that your attributes on One Step Checkout'.
                    'have the same value for Required field as on attribute edit page'.
                    '(Stores → Attributes → Customer and Customer Address Attributes → Attribute Edit Page).'.
                    'Otherwise errors may happen.'
                );
            } else {
                $message = __(
                    'Please make sure that your attributes on One Step Checkout ' .
                    'have the same value for Required field as on attribute edit page ' .
                    '(Stores → Attributes → Customer and Customer Address Attributes). Otherwise errors may happen.'
                );
            }
            $this->messageManager->addNotice($message);
            $this->isDisplayed = true;
        }

        return $this;
    }
}
