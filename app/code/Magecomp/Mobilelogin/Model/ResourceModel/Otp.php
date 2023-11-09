<?php
namespace Magecomp\Mobilelogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Otp
 * Magecomp\Mobilelogin\Model\ResourceModel
 */
class Otp extends AbstractDb
{
    /**
     * Otp constructor.
     * @param Context $context
     * @param null $resourcePrefix
     */
    public function __construct(
        Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mobilelogin_otp', 'id');
    }
}
