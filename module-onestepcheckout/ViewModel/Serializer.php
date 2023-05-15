<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\ViewModel;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Serializer view model
 */
class Serializer implements ArgumentInterface
{
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Serialize to json
     *
     * @param mixed $data
     * @return string
     */
    public function serializeToJson(mixed $data): string
    {
        return $this->serializer->serialize($data);
    }
}
