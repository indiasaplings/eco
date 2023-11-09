<?php
namespace Magecomp\Mobilelogin\Model\Config;

/**
 * Class Layout
 * Magecomp\Mobilelogin\Model\Config
 */
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => ''],
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'template', 'label' => __('Template')]
        ];
    }
}
