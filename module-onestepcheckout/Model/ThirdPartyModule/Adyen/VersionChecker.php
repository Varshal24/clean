<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Adyen;

use Magento\Framework\Module\PackageInfo;

class VersionChecker
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @param PackageInfo $packageInfo
     */
    public function __construct(PackageInfo $packageInfo)
    {
        $this->packageInfo = $packageInfo;
    }

    /**
     * Check if AdyenPayment version matched
     *
     * @param string $operator
     * @param string $version
     * @return bool
     */
    public function is(string $operator, string $version): bool
    {
        $moduleVersion = $this->packageInfo->getVersion('Adyen_Payment');
        return version_compare($moduleVersion, $version, $operator);
    }
}