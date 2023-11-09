<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magecomp\Mobilelogin\Model\OtpFactory;
use Magento\Framework\Controller\ResultFactory;

class Ajaxcheckregotp extends \Magento\Framework\App\Action\Action
{
    protected $_modelRegOtpFactory;
    protected $resultFactory;


    public function __construct(
        Context $context,
        OtpFactory $modelRegOtpFactory,
         ResultFactory $resultFactory
    )
    {
        $this->_modelRegOtpFactory = $modelRegOtpFactory;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $mobile = $this->getRequest()->getParams('mobile');
        $otp = $this->getRequest()->getParams('otp');
       
        $otpModels = $this->_modelRegOtpFactory->create();
        $collection = $otpModels->getCollection();
        $collection->addFieldToFilter('mobile', $mobile);
        $collection->addFieldToFilter('random_code', $otp);

        if (count($collection) == '1') {
            $data = "true";
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        } else {
            $data = "false";
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        }
    }
}