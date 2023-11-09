<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Framework\App\Action\Action;

/**
 * Class Forgotsend
 * Magecomp\Mobilelogin\Controller\Index
 */
class Forgotsend extends Action implements HttpPostActionInterface
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
     * Forgotsend constructor.
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
            if (isset($data['otp_reset_type']) && $data['otp_reset_type'] == "mobile") {
                if ($data['forgotmob']) {

                    $jsonOutput = $this->_helperData->sendForgotPasswordOTP($data, $websiteId);
                } else {
                    $jsonOutput = ["status"=>false, "message"=>__("Please enter correct mobile no.")];
                }
            } elseif (isset($data['otp_reset_type']) && $data['otp_reset_type'] == "email") {
                if ($data['email']) {
                    $jsonOutput = $this->_helperData->sendForgotPasswordEmail($data, $websiteId);
                } else {
                    $jsonOutput = ["status"=>false, "message"=>__("--Please enter correct email address.")];
                }
            } else {
                $jsonOutput = ["status"=>false, "message"=>__("This operation is not permitted.")];
            }
        }
        $jsonResult = $this->_jsonResultFactory->create();
        $jsonResult->setData($jsonOutput);
        return $jsonResult;
    }
}
