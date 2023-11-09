<?php
namespace Magecomp\Mobilelogin\Model\Config;

/**
 * Class Routetype
 * Magecomp\Mobilelogin\Model\Config
 */
class Routetype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Promotional Message')],
            ['value' => 4, 'label' => __('Transactional Message')]
        ];
    }
}
