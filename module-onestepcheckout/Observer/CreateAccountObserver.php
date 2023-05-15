<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\OneStepCheckout\Model\Data\Storage;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;

class CreateAccountObserver implements ObserverInterface
{
    /**
     * @param Storage $storage
     * @param OrderCustomerManagementInterface $orderCustomerService
     * @param EmailNotificationInterface $emailNotification
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        private Storage $storage,
        private OrderCustomerManagementInterface $orderCustomerService,
        private EmailNotificationInterface $emailNotification,
        private MessageManagerInterface $messageManager
    ) {
    }

    /**
     * Create customer account
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $isShouldCreatedAccount = $this->storage->get(Storage::IS_SHOULD_CREATED_ACCOUNT);
        if ($isShouldCreatedAccount) {
            $orderId = (int) $observer->getOrder()->getEntityId();
            try {
                $customer = $this->orderCustomerService->create($orderId);
                $this->emailNotification->credentialsChanged($customer, $customer->getEmail());
                $this->messageManager->addSuccessMessage(
                    __('Customer account has been created. Please check your email for the confirmation link.')
                );
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage(
                    __('A customer with the same email address already exists.')
                );
            }
        }
    }
}
