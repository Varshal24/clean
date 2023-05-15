<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Order;

use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class SuccessPageMessage extends Template
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        MessageManagerInterface $messageManager,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        parent::__construct($context, $data);
    }

    /**
     * Get message success for group success_create_customer
     *
     * @return array
     */
    public function getMessagesSuccessCreateCustomer(): array
    {
        return $this->messageManager
            ->getMessages(true, 'success_create_customer')
            ->getItemsByType('success');
    }
}
