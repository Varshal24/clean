<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Customer\Builder;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Authorization\Model\UserContextInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Customer\Attribute\ValueProvider;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;

class Value implements UiComponentBuilderInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var ValueProvider
     */
    private $valueProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param UserContextInterface $userContext
     * @param ValueProvider $valueProvider
     * @param Config $config
     */
    public function __construct(
        UserContextInterface $userContext,
        ValueProvider $valueProvider,
        Config $config
    ) {
        $this->userContext = $userContext;
        $this->valueProvider = $valueProvider;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        $userType = $this->userContext->getUserType();
        if ($userType != UserContextInterface::USER_TYPE_CUSTOMER) {
            return $definition;
        }

        $value = isset($config['backendType']) && $config['backendType'] == 'static'
            ? $this->valueProvider->getStaticAttributeValue($this->userContext->getUserId(), $code)
            : $this->valueProvider->getAttributeValue($this->userContext->getUserId(), $code);

        if ($value) {
            if ($this->config->canDisplayFilledCustomerInfoFields()) {
                $definition['value'] = $value;
            } else {
                $definition['visible'] = '0';
            }
        }

        return $definition;
    }
}
