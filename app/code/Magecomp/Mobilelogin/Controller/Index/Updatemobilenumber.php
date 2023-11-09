<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;
use Magento\Framework\App\Action\Action;

/**
 * Class Updatemobilenumber
 * Magecomp\Mobilelogin\Controller\Index
 */
class Updatemobilenumber extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $_jsonResultFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var Customer
     */
    protected $customer;
    protected $messageManager;

    /**
     * @var CustomerResourceFactory
     */
    protected $customerResourceFactory;
    public $_storeManager;

    /**
     * @var CustomerResource
     */
    protected $customerResource;
    public $_helperdata;

    /**
     * Updatemobilenumber constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     * @param CustomerFactory $customerFactory
     * @param Customer $customer
     * @param CustomerData $customerData
     * @param CustomerResource $customerResource
     * @param CustomerResourceFactory $customerResourceFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CustomerFactory $customerFactory,
        Customer $customer,
        CustomerData $customerData,
        CustomerResource $customerResource,
        StoreManagerInterface $storeManager,
        CustomerResourceFactory $customerResourceFactory,
        \Magecomp\Mobilelogin\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
         parent::__construct($context);

        $this->_jsonResultFactory = $jsonResultFactory;
        $this->customerFactory    = $customerFactory;
        $this->customer    = $customer;
        $this->customerData    = $customerData;
        $this->_storeManager = $storeManager;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->customerResource = $customerResource;
        $this->_messageManager = $messageManager;
        $this->_helperdata = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $jsonOutput = ["status"=>false, "message"=>__("This operation is not permitted.")];
        } else {

            $mobile = (string)$this->getRequest()->getParam('mobile');
            $otp = $this->getRequest()->get('verifyotp');

            $websiteId = $this->_storeManager->getWebsite()->getId();
            $data = $this->getRequest()->getParams();
            $returnVal = $this->_helperdata->verifyRegistrationOTP($data,$websiteId);

            if($returnVal['status'] == 'true') {
                $customerId = (string)$this->getRequest()->getParam('userId');
                if ($mobile != "") {
                    $this->customerData = $this->customer->getDataModel();
                    $collection = $this->customerFactory->create()->getCollection()
                        ->addFieldToFilter("mobilenumber", $mobile)
                        ->addFieldToFilter("entity_id", ["neq" => $customerId]);
                    if (count($collection) > 0) {
                        $jsonOutput = ["status" => false, "message" => __("Customer is already registered with same mobile no.")];
                    } else {
                        $this->customerData->setId($customerId);
                        $this->customerData->setCustomAttribute('mobilenumber', $mobile);
                        $this->customer->updateData($this->customerData);
                        $this->customerResource->saveAttribute($this->customer, 'mobilenumber');
                        $jsonOutput = ["status" => true, "message" => __("Mobile number updated successfully.")];
                    }
                }

            }else{
                $jsonOutput = ["status" => false, "message" => __("There is some issue. Please try again later.")];
            }
        }
        $jsonResult = $this->_jsonResultFactory->create();
        $jsonResult->setData($jsonOutput);
        return $jsonResult;
    }
}
