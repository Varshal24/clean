<?php
namespace Aheadworks\OneStepCheckout\Plugin\Checkout\Block;

use Aheadworks\OneStepCheckout\Block\Checkout;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Klarna\Kp\SessionFactory as KpSessionFactory;
use Klarna\Kp\Model\Session as KlarnaKpSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Klarna\Core\Exception as KlarnaCoreException;
use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OnepagePlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Checkout\Block
 */
class OnepagePlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var KpSessionFactory
     */
    private $kpSessionFactory;

    /**
     * @param ScopeConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param KpSessionFactory $kpSessionFactory
     */
    public function __construct(
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        KpSessionFactory $kpSessionFactory
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->kpSessionFactory = $kpSessionFactory;
    }

    /**
     * Initialize Klarna Payment session before get js layout
     *
     * @param Checkout $subject
     * @return array
     * @throws KlarnaCoreException
     * @throws KlarnaApiException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetJsLayout(Checkout $subject)
    {
        $kpSession = $this->getKpSession();
        $store = $this->storeManager->getStore();
        if ($kpSession && $this->config->isSetFlag(
            sprintf('payment/%s/active', Kp::METHOD_CODE),
            ScopeInterface::SCOPE_STORES,
            $store
        )) {
            $kpSession->init();
        }
        return [];
    }

    /**
     * Retrieve Klarna Kp Session
     *
     * @return KlarnaKpSession|null
     */
    private function getKpSession()
    {
        return $this->kpSessionFactory->create();
    }
}
