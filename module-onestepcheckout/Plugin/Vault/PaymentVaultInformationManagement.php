<?php
namespace Aheadworks\OneStepCheckout\Plugin\Vault;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Vault\Api\PaymentMethodListInterface;
use Magento\Store\Model\StoreManagerInterface;

class PaymentVaultInformationManagement
{
    /**
     * @var PaymentMethodListInterface
     */
    private $vaultPaymentMethodList;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param PaymentMethodListInterface $vaultPaymentMethodList
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        PaymentMethodListInterface $vaultPaymentMethodList,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->vaultPaymentMethodList = $vaultPaymentMethodList;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Set available vault method code without index to payment
     *
     * This fix is already added in Vault module starting from M2.4.2
     *
     * @param PaymentInformationManagementInterface $subject
     * @param string $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $magentoVersion = $this->productMetadata->getVersion();
        if (version_compare($magentoVersion, '2.4.1', '<=')) {
            $availableMethods = $this->vaultPaymentMethodList->getActiveList($this->storeManager->getStore()->getId());
            foreach ($availableMethods as $availableMethod) {
                if (strpos($paymentMethod->getMethod(), $availableMethod->getCode()) !== false) {
                    $paymentMethod->setMethod($availableMethod->getCode());
                    break;
                }
            }
        }
    }
}
