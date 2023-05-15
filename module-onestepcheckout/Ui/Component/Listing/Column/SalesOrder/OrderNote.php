<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Ui\Component\Listing\Column\SalesOrder;

use Magento\Ui\Component\Listing\Columns\Column;

class OrderNote extends Column
{
    /**
     * Prepare order note
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $orderNote = $item['aw_order_note'] ?? null;

                if ($orderNote) {
                    $item[$this->getName()] = $this->getShortOrderNote($orderNote);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Resolve short order note
     *
     * @param string $orderNote
     * @return string
     */
    private function getShortOrderNote(string $orderNote): string
    {
        $maxLength = 255;

        if (strlen($orderNote) > $maxLength) {
            $orderNote = substr($orderNote, 0, $maxLength - 4) . ' ...';
        }

        return $orderNote;
    }
}
