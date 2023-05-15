<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Klarna\Kp;

use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Klarna\Kp\Model\Session as KlarnaKpSession;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class SessionFactory
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Klarna\Kp
 */
class SessionFactory
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ModuleManager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Create Klarna Payment Session class
     *
     * @return KlarnaKpSession|null
     */
    public function create()
    {
        return $this->moduleManager->isKlarnaKpEnabled()
            ? $this->objectManager->create(KlarnaKpSession::class)
            : null;
    }
}
