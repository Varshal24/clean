<?php
namespace Aheadworks\OneStepCheckout\Model\Layout;

use Magento\Framework\ObjectManagerInterface;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LayoutProcessorProvider
 * @package Aheadworks\OneStepCheckout\Model\Layout
 */
class LayoutProcessorProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LayoutProcessorInterface[]
     */
    private $metadataInstances = [];

    /**
     * @var array
     */
    private $processors = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger,
        $processors = []
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->processors = $processors;
    }

    /**
     * Retrieves array of layout processors
     *
     * @return LayoutProcessorInterface[]
     */
    public function getLayoutProcessors()
    {
        if (empty($this->metadataInstances)) {
            foreach ($this->processors as $layoutProcessorClassName) {
                $layoutProcessor = $this->createLayoutProcessor($layoutProcessorClassName);
                if ($layoutProcessor instanceof LayoutProcessorInterface) {
                    $this->metadataInstances[$layoutProcessorClassName] = $layoutProcessor;
                }
            }
        }
        return $this->metadataInstances;
    }

    /**
     * Create layout processor object in a safe way
     *
     * @param string $layoutProcessorClassName
     * @return object|null
     */
    private function createLayoutProcessor($layoutProcessorClassName)
    {
        try {
            $layoutProcessor = $this->objectManager->create($layoutProcessorClassName);
        } catch (\Exception $exception) {
            $layoutProcessor = null;
            $this->logger->warning($exception->getMessage());
        }
        return $layoutProcessor;
    }
}
