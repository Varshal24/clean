<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address;

class TemplateResolver
{
    /**
     * @var array
     */
    private $templateMap = [
        'image' => 'ui/form/element/media',
        'checkbox' => 'ui/form/element/select',
        'input' => 'Aheadworks_OneStepCheckout/form/element/input'
    ];

    /**
     * @param array $templateMap
     */
    public function __construct(array $templateMap = [])
    {
        $this->templateMap = array_merge($this->templateMap, $templateMap);
    }

    /**
     * Resolve template
     *
     * @param string $formElement
     * @return string
     */
    public function resolve($formElement)
    {
        return $this->templateMap[$formElement] ?? 'ui/form/element/' . $formElement;
    }
}
