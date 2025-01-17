<?php


namespace Lof\Cfcheckout\Model;


class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const ENVIRONMENT_PROD   = 'prod';
    const ENVIRONMENT_TEST   = 'test';

    /**
     * Possible environment types
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_TEST,
                'label' => 'Test',
            ],
            [
                'value' => self::ENVIRONMENT_PROD,
                'label' => 'Prod'
            ]
        ];
    }
}
