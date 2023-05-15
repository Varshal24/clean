<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Helper;

use Magento\Framework\View\Asset\Repository as AssetRepository;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Adyen\VersionChecker as AdyenVersionChecker;

class AdyenStylesheetResolver
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var AdyenVersionChecker
     */
    private $versionChecker;

    /**
     * @param AssetRepository $assetRepository
     * @param AdyenVersionChecker $versionChecker
     */
    public function __construct(
        AssetRepository $assetRepository,
        AdyenVersionChecker $versionChecker
    ) {
        $this->assetRepository = $assetRepository;
        $this->versionChecker = $versionChecker;
    }

    /**
     * Resolve path to adyen css file
     *
     * @return string
     */
    public function resolveUrlToAdyenCss(): string
    {
        $path = $this->versionChecker->is('<', '8.1.0')
            ? 'Adyen_Payment::css/styles.css'
            : 'Adyen_Payment::css/adyen.css';

        return $this->assetRepository->getUrl($path);
    }
}
