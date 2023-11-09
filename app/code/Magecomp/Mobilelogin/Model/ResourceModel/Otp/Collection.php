<?php
namespace Magecomp\Mobilelogin\Model\ResourceModel\Otp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * Magecomp\Mobilelogin\Model\ResourceModel\Otp
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magecomp\Mobilelogin\Model\Otp::class, \Magecomp\Mobilelogin\Model\ResourceModel\Otp::class);
    }
}
