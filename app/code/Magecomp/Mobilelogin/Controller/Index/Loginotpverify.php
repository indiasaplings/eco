<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header as HTTPHeader;

/**
 * Class Loginotpverify
 * Magecomp\Mobilelogin\Controller\Index
 */
class Loginotpverify extends Action implements HttpPostActionInterface
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
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var HTTPHeader
     */
    private $httpHeader;

    /**
     * Loginotpverify constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param MagecompHelper $helperData
     * @param RemoteAddress $remoteAddress
     * @param HTTPHeader $httpHeader
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        MagecompHelper $helperData,
        RemoteAddress $remoteAddress,
        HTTPHeader $httpHeader
    ) {
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        $this->session = $customerSession;
        $this->remoteAddress = $remoteAddress;
        $this->httpHeader = $httpHeader;
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
            $jsonOutput = $this->_helperData->verifyLoginOTP($data, $websiteId);

            if ($jsonOutput['status']) {
                $customerCollection = $this->_helperData->checkCustomerExists($data['mobile'], "mobile", $websiteId);
                if ($customerCollection) {
                    $customer = $customerCollection->getFirstItem();
                    $this->session->setCustomerAsLoggedIn($customer);
                    $this->session->regenerateId();
                    if ($this->_helperData->isEnableLoginEmail()) {
                        $this->_helperData->sendMail(
                            $this->remoteAddress->getRemoteAddress(),
                            $customer->getEmail(),
                            $this->httpHeader->getHttpUserAgent()
                        );
                    }
                    $redirectUrl = $this->_helperData->getAfterLoginRedirect();

                    $jsonOutput = ["status"=>true,"redirectUrl"=>$redirectUrl];
                }
            } else {
                $jsonOutput = ["status"=>false, "message"=>__("Customer does not exists.")];
            }
        }
        $jsonResult = $this->_jsonResultFactory->create();
        $jsonResult->setData($jsonOutput);
        return $jsonResult;
    }
}
