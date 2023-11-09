<?php
namespace Magecomp\Mobilelogin\Model\Config;

/**
 * Class Template
 * Magecomp\Mobilelogin\Model\Config
 */
class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => 0,'label' => __('--Please Select--')];
        for ($len = 1; $len <=10; $len++) {
            $options[] = ['value' => $len,'label' => __('Template ').$len];
        }
        return $options;
    }
}
