<?php
namespace Aheadworks\OneStepCheckout\Block;

use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\OneStepCheckout\Model\Layout\LayoutProcessorProvider;
use Magento\Framework\Serialize\SerializerInterface;

class Checkout extends Template
{
    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var CompositeConfigProvider
     */
    private $configProvider;

    /**
     * @var LayoutProcessorProvider
     */
    private $layoutProcessorProvider;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Context $context
     * @param FormKey $formKey
     * @param CompositeConfigProvider $configProvider
     * @param LayoutProcessorProvider $layoutProcessorProvider
     * @param HttpContext $httpContext
     * @param SerializerInterface $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        CompositeConfigProvider $configProvider,
        LayoutProcessorProvider $layoutProcessorProvider,
        HttpContext $httpContext,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
        $this->configProvider = $configProvider;
        $this->layoutProcessorProvider = $layoutProcessorProvider;
        $this->httpContext = $httpContext;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessorProvider->getLayoutProcessors() as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return $this->serializer->serialize($this->jsLayout);
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Retrieve serialized checkout configuration
     *
     * @return string
     */
    public function getSerializedCheckoutConfig(): string
    {
        return $this->serializer->serialize($this->configProvider->getConfig());
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }
}
