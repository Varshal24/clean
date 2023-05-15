<?php
namespace Aheadworks\OneStepCheckout\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\AddressFactory;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Aheadworks\OneStepCheckout\Api\CustomerAddressManagementInterface;
use Aheadworks\OneStepCheckout\Model\Address\Attribute\CustomAttributesFormatter;

/**
 * Class CustomerAddressManagement
 *
 * @package Aheadworks\OneStepCheckout\Model
 */
class CustomerAddressManagement implements CustomerAddressManagementInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CustomAttributesFormatter
     */
    private $customAttributesFormatter;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param AddressFactory $addressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomAttributesFormatter $customAttributesFormatter
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        AddressFactory $addressFactory,
        AddressRepositoryInterface $addressRepository,
        CustomAttributesFormatter $customAttributesFormatter
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
        $this->customAttributesFormatter = $customAttributesFormatter;
    }

    /**
     * @inheritdoc
     */
    public function updateAddress(
        $customerId,
        QuoteAddressInterface $address
    ) {
        $customerAddress = $this->addressRepository->getById($address->getCustomerAddressId());
        if ($customerAddress->getCustomerId() != $customerId) {
            throw new LocalizedException(__('Address cannot be updated'));
        }

        /** @var Address $address */
        $newAddress = $this->addressFactory->create();
        $this->customAttributesFormatter->format($address);
        $newAddress->setData($address->getData());

        $this->dataObjectHelper->mergeDataObjects(
            CustomerAddressInterface::class,
            $customerAddress,
            $newAddress->getDataModel()
        );

        return $this->addressRepository->save($customerAddress);
    }
}