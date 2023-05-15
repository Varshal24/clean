<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Vat;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class VatValidation extends Action implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param Vat $vatChecker
     */
    public function __construct(
        protected readonly Context $context,
        private readonly Vat $vatChecker
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $request = $this->getRequest();
        $response = [
            'is_valid' => false
        ];

        if ($request->isPost()) {
            $countryId = $request->getPostValue('country_id');
            $vatNumber = $request->getPostValue('vat_number');
            $result = $this->vatChecker->checkVatNumber($countryId, $vatNumber);
            $response = $result->getData();
        }

        return $resultJson->setData($response);
    }
}
