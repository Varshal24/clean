<?php
namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Layout\Processor\Totals;

use Aheadworks\OneStepCheckout\Model\Layout\Processor\Totals\Sorter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Layout\Processor\Totals\Sorter
 */
class SorterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Sorter
     */
    private $sorter;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var MockObject|Store
     */
    private MockObject|Store $store;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->store = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sorter = $objectManager->getObject(
            Sorter::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    public function testSort()
    {
        $totalCode = 'total';
        $sortOrder = 5;
        $storeId = 1;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->store);

        $this->store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('sales/totals_sort', ScopeInterface::SCOPE_STORE)
            ->willReturn([$totalCode => $sortOrder]);

        $this->assertEquals(
            [
                $totalCode => [
                    'component' => 'TotalComponent',
                    'config' => [],
                    'sortOrder' => $sortOrder
                ]
            ],
            $this->sorter->sort(
                [
                    $totalCode => [
                        'component' => 'TotalComponent',
                        'config' => []
                    ]
                ]
            )
        );
    }
}
