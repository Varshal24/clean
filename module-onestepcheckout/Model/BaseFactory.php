<?php
namespace Aheadworks\OneStepCheckout\Model;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;

class BaseFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleListInterface $moduleList
     * @param string $className
     * @param string $moduleName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleListInterface $moduleList,
        string $className = '',
        string $moduleName = ''
    ) {
        $this->objectManager = $objectManager;
        $this->moduleList = $moduleList;
        $this->className = $className;
        $this->moduleName = $moduleName;
    }

    /**
     * Create an instance of specific class only in case if corresponding module is enabled
     *
     * @param array $data
     * @return object|null
     */
    public function create(array $data = [])
    {
        if (empty($this->className)
            || empty($this->moduleName)
            || !$this->moduleList->has($this->moduleName)
        ) {
            return null;
        }
        return $this->objectManager->create($this->className, $data);
    }
}
