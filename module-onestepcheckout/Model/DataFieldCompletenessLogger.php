<?php
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface;
use Aheadworks\OneStepCheckout\Api\DataFieldCompletenessLoggerInterface;
use Aheadworks\OneStepCheckout\Model\ResourceModel\FieldCompleteness\Logger;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class DataFieldCompletenessLogger
 * @package Aheadworks\OneStepCheckout\Model
 */
class DataFieldCompletenessLogger implements DataFieldCompletenessLoggerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param Logger $logger
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        Logger $logger,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($cartId, array $fieldCompleteness)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');

        $quoteId = $quoteIdMask->getQuoteId() ?: $cartId;

        $logData = [];
        foreach ($fieldCompleteness as $item) {
            $logData[] = $this->dataObjectProcessor->buildOutputDataArray(
                $item,
                DataFieldCompletenessInterface::class
            );
        }

        $this->logger->log($quoteId, $logData);
    }
}
