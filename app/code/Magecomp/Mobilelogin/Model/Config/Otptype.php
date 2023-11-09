<?php
namespace Magecomp\Mobilelogin\Model\Config;

/**
 * Class Otptype
 * Magecomp\Mobilelogin\Model\Config
 */
class Otptype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'N', 'label' => __('Numeric Only')],
            ['value' => 'AN', 'label' => __('Alpha Numeric')]
        ];
    }
}
