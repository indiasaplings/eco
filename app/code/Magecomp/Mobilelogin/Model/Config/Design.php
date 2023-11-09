<?php
namespace Magecomp\Mobilelogin\Model\Config;

/**
 * Class Design
 * Magecomp\Mobilelogin\Model\Config
 */
class Design implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'standardlayout', 'label' => __('Standard Layout')],
            ['value' => 'ultimatelayout' , 'label' => __('Ultimate Layout')]

        ];
    }
}
