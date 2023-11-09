<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Framework\App\Action\Action;

/**
 * Class ForgotPasswordPost
 * Magecomp\Mobilelogin\Controller\Index
 */
class ForgotPasswordPost extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $_jsonResultFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var MagecompHelper
     */
    protected $_helperData;

    /**
     * ForgotPasswordPost constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     * @param StoreManagerInterface $storeManager
     * @param MagecompHelper $helperData
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        StoreManagerInterface $storeManager,
        MagecompHelper $helperData
    ) {
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $jsonOutput = ["status"=>false, "message"=>__("This operation is not permitted.")];
        } else {
            $websiteId = $this->_storeManager->getWebsite()->getId();
            $data = $this->getRequest()->getParams();
            $data['mobile'] = $data['mobile'];
            $data['otp'] = $data['otp'];
            $isVerified = $this->_helperData->checkOTPisVerified(
                $data,
                $this->_helperData::FORGOTPASSWORD_OTP_TYPE,
                $websiteId
            );
            if (count($isVerified) == 1) {
                $jsonOutput = $this->_helperData->resetForgotPassword($data, $websiteId);
            } else {
                $jsonOutput = ["status"=>false, "message"=>__("Mobile no is not verified.")];
            }
        }
        $jsonResult = $this->_jsonResultFactory->create();
        $jsonResult->setData($jsonOutput);
        return $jsonResult;
    }
}
