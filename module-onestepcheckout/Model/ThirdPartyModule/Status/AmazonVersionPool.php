<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon\StatusInterface;

/**
 * Class AmazonVersionPool
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class AmazonVersionPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $versionProcessors = [];

    /**
     * @var StatusInterface[]
     */
    private $processorInstance = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $versionProcessors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $versionProcessors = []
    ) {
        $this->objectManager = $objectManager;
        $this->versionProcessors = $versionProcessors;
    }

    /**
     * Get Amazon version processor according to module version
     *
     * @param string $moduleVersion
     * @return StatusInterface
     * @throws \Exception
     * todo re-check if this method is used anywhere M2OSC-1387
     */
    public function getAmazonVersionProcessor(string $moduleVersion): StatusInterface
    {
        if (isset($this->processorInstance[$moduleVersion])) {
            return $this->processorInstance[$moduleVersion];
        }

        if (isset($this->versionProcessors[$moduleVersion])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors[$moduleVersion])) {
                $this->processorInstance[$moduleVersion]
                    = $this->objectManager->create($this->versionProcessors[$moduleVersion]);
                return $this->processorInstance[$moduleVersion];
            } else {
                throw new LocalizedException(
                    sprintf('Class not found %s', $this->versionProcessors[$moduleVersion])
                );
            }
        } elseif (!isset($this->processorInstance['default'])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors['default'])) {
                $this->processorInstance['default']
                    = $this->objectManager->create($this->versionProcessors['default']);
                return $this->processorInstance['default'];
            }
        } else {
            return $this->processorInstance['default'];
        }

        throw new LocalizedException(__('Amazon version processor cannot be resolved'));
    }

    /**
     * Check if Amazon version processor can be created
     *
     * @param string $amazonVersionProcessor
     * @return bool
     */
    private function isAmazonVersionProcessorCanBeCreated(string $amazonVersionProcessor): bool
    {
        return class_exists($amazonVersionProcessor);
    }
}
