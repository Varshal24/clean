<?php
namespace Aheadworks\OneStepCheckout\Model\Customer\CustomerInfo;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

/**
 * Assign customer info data to customer
 */
class CustomerAssigner
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Converter
     */
    private $customerInfoConverter;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Converter $customerInfoConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param AddressRegistry $addressRegistry
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Converter $customerInfoConverter,
        DataObjectHelper $dataObjectHelper,
        AddressRegistry $addressRegistry
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerInfoConverter = $customerInfoConverter;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->addressRegistry = $addressRegistry;
    }

    /**
     * Assign customer info from order
     *
     * @param Order $order
     * @throws \Exception
     */
    public function assignFromOrder($order)
    {
        if ($order->getCustomerId() && $order->getAwOscCustomerInfo()) {
            $customerInfo = $this->customerInfoConverter->toArrayFromSerializedString($order->getAwOscCustomerInfo());
            $customer = $this->customerRepository->getById($order->getCustomerId());
            $this->dataObjectHelper->populateWithArray(
                $customer,
                $customerInfo,
                CustomerInterface::class
            );

            $this->disableAddressValidation($customer);
            $this->customerRepository->save($customer);
        }
    }

    /**
     * Disable customer address validation
     *
     * No need to validate customer address while saving customer information
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }
}
