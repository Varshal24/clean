<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address\Builder;

use Magento\Framework\App\ProductMetadataInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiComponentBuilderInterface;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\UiConfigHelper;

class AddressSpecific implements UiComponentBuilderInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var UiConfigHelper
     */
    private $uiConfigHelper;

    /**
     * @var bool
     */
    private $shouldRemoveOptions;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param UiConfigHelper $uiConfigHelper
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        UiConfigHelper $uiConfigHelper
    ) {
        $this->productMetadata = $productMetadata;
        $this->uiConfigHelper = $uiConfigHelper;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($code, $config, $definition = [], $additionalConfig = [])
    {
        if (isset($additionalConfig['customScope'])) {
            $definition['config']['customScope'] = $additionalConfig['customScope'];
            $dataScope = isset($config['system']) && $config['system'] == true
                ? $additionalConfig['customScope'] . '.' . $code
                : $additionalConfig['customScope'] . '.custom_attributes.' . $code;
            $definition['dataScope'] = $dataScope;
        }

        $providerName = $additionalConfig['provider'] ?? '';
        $definition['provider'] = $providerName;
        $definition['countryFieldIncludedRow'] = $additionalConfig['countryFieldIncludedRow'] ?? null;

        if (($code === 'region_id' || $code === 'country_id') && $this->shouldRemoveOptions()) {
            unset($definition['options']);
            $definition['deps'] = [$providerName];
            $definition['imports'] = [
                'initialOptions' => 'index = ' . $providerName . ':dictionaries.' . $code,
                'setOptions' => 'index = ' . $providerName . ':dictionaries.' . $code
            ];
        }
        if (isset($additionalConfig['visible'])) {
            $definition['visible'] = $additionalConfig['visible'];
        } elseif ($this->uiConfigHelper->isAddressFieldInitiallyHidden($code)) {
            $definition['visible'] = false;
        } else {
            $definition['visible'] = true;
        }

        return $definition;
    }

    /**
     * Should remove country and region options
     *
     * @return bool
     */
    private function shouldRemoveOptions()
    {
        if ($this->shouldRemoveOptions === null) {
            $this->shouldRemoveOptions = version_compare($this->productMetadata->getVersion(), '2.1.8', '>=');
        }

        return $this->shouldRemoveOptions;
    }
}
