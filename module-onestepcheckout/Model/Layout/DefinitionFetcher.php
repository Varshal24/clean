<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Layout;

use Magento\Framework\Config\Converter\Dom\Flat as FlatConverter;
use Magento\Framework\Config\Dom\ArrayNodeConfig;
use Magento\Framework\Config\Dom\NodePathMatcher;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\LayoutFactory;

class DefinitionFetcher
{
    /**
     * @var Element[]
     */
    private array $layoutUpdates = [];

    /**
     * @var FlatConverter
     */
    private FlatConverter $flatConverter;

    /**
     * @param LayoutFactory $layoutFactory
     * @param InterpreterInterface $argumentInterpreter
     * @param RecursiveMerger $recursiveMerger
     */
    public function __construct(
        private readonly LayoutFactory $layoutFactory,
        private readonly InterpreterInterface $argumentInterpreter,
        private readonly RecursiveMerger $recursiveMerger
    ) {
        $this->flatConverter = $this->initFlatConverter();
    }

    /**
     * Init flat converter
     *
     * @return FlatConverter
     */
    private function initFlatConverter(): FlatConverter
    {
        return new FlatConverter(
            new ArrayNodeConfig(new NodePathMatcher(), ['(/item)+' => 'name'])
        );
    }

    /**
     * Fetch arguments definition data
     *
     * @param array|string $handles
     * @param string $xpath
     * @return array
     */
    public function fetchArgs(array|string $handles, string $xpath): array
    {
        $result = [];
        try {
            $layoutUpdateXml = $this->getLayoutUpdate($handles);
            $searchResult = $layoutUpdateXml->xpath($xpath);

            if ($searchResult) {
                foreach ($searchResult as $element) {
                    $elementDom = dom_import_simplexml($element);
                    $data = $this->argumentInterpreter->evaluate(
                        $this->flatConverter->convert($elementDom)
                    );
                    $result = $this->recursiveMerger->merge($result, $data);
                }
            }
        } catch (\Exception $e) {
            $result = [];
        }
        return $result;
    }

    /**
     * Get layout update instance
     *
     * @param array|string $handles
     * @return Element
     * @throws LocalizedException
     */
    private function getLayoutUpdate(array|string $handles): Element
    {
        $key = is_array($handles)
            ? implode('-', $handles)
            : $handles;
        if (!isset($this->layoutUpdates[$key])) {
            $this->layoutUpdates[$key] = $this->layoutFactory->create()
                ->getUpdate()
                ->load($handles)
                ->asSimplexml();
        }
        return $this->layoutUpdates[$key];
    }
}
