<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model\Config\Source\Customization;

class CustomValidation
{
    /**
     * @param array $validations
     */
    public function __construct(
        private array $validations = []
    ) {
    }

    /**
     * Retrieve all custom validations
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->validations;
    }

    /**
     * Retrieve custom validations by code
     *
     * @param string $code
     * @return array
     */
    public function getByCode(string $code): array
    {
        return $this->validations[$code] ?? [];
    }
}
