<?php
namespace Magecomp\Mobilelogin\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;


use Magento\Framework\Exception\AuthenticationException;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class MobilePost
 * Magecomp\Mobilelogin\Model
 */
class MobilePost implements \Magecomp\Mobilelogin\Api\MobilePostInterface
{
    /**
     * MobilePost constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagement $accountManagement
     * @param CollectionFactory $customerCollecction
     * @param AuthenticationInterface $customerAuthentication 
     * @param MagecompHelper $helperData
     * @param CustomerFactory $customerFactory
     * @param PsrLogger $logger
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TokenModelFactory $tokenModelFactory,
        AccountManagement $accountManagement,
        CustomerResource $customerResource,
        CollectionFactory $customerCollecction,
		AuthenticationInterface $customerAuthentication,
        MagecompHelper $helperData,
        CustomerFactory $customerFactory,
        Customer $customer,
        CustomerData $customerData,
        PsrLogger $logger,
        JsonHelper $jsonHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customer    = $customer;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->accountManagement = $accountManagement;
        $this->customerResource = $customerResource;
        $this->customerCollecction = $customerCollecction;
		$this->customerAuthentication = $customerAuthentication;
        $this->_helperdata = $helperData;
        $this->_customerFactory = $customerFactory;
        $this->customerData    = $customerData;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginOTP($mobileNumber, $websiteId)
    {
        try {
            if (empty($mobileNumber) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->sendLoginOTP(["loginotpmob"=>$mobileNumber], $websiteId);
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginOTPVerify($mobileNumber, $otp, $websiteId)
    {
        try {
            if (empty($mobileNumber) || empty($otp) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->verifyLoginOTP(["mobile"=>$mobileNumber,"verifyotp"=>$otp], $websiteId);
            if ($response['status']) {
                $customerCollection = $this->_helperdata->checkCustomerExists($mobileNumber, "mobile", $websiteId);
                if (count($customerCollection) > 0) {
                    $customer = $customerCollection->getFirstItem();
                    $token = $this->tokenModelFactory->create()->createCustomerToken($customer->getId())->getToken();
                    $response['token'] = $token;
                } else {
                    $response = ["status"=>false, "message"=>__("Customer does not exists.")];
                }
            }
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }
    public function getApiUpdateOTPCode($newMobileNumber, $websiteId, $customerId, $oldMobileNumber)
    {
        try {
            if (empty($newMobileNumber) || (empty($websiteId) && empty($websiteId)>0) || empty($customerId) || empty($oldMobileNumber)) {

                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
            $collection = $customerObj->addAttributeToSelect('*')
                ->addAttributeToFilter('mobilenumber', $oldMobileNumber)
                ->addAttributeToFilter('entity_id', $customerId)
                ->load();

            if(count($collection) > 0 ){
                $response = $this->_helperdata->sendUpdateOTPCode($newMobileNumber, $websiteId);
            }else{
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Customer does not exist.")]);
            }

            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    public function updateNumberVerifyOTP($newMobileNumber, $otp, $customerId, $oldMobileNumber, $websiteId){
        try {
            if (empty($newMobileNumber) || (empty($websiteId) && empty($websiteId)>0) || empty($customerId) || empty($oldMobileNumber) || empty($otp)) {

                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
            $collection = $customerObj->addAttributeToSelect('*')
                ->addAttributeToFilter('mobilenumber', $oldMobileNumber)
                ->addAttributeToFilter('entity_id', $customerId)
                ->load();

            if(count($collection) > 0 ){

                $response = $this->_helperdata->verifyRegistrationOTP(
                    ["mobile"=>$newMobileNumber, "verifyotp"=>$otp],
                    $websiteId
                );
                if($response['status'] == 1){

                    $this->customerData->setId($customerId);
                    $this->customerData->setCustomAttribute('mobilenumber', $newMobileNumber);
                    $this->customer->updateData($this->customerData);
                    $this->customerResource->saveAttribute($this->customer, 'mobilenumber');
                    $jsonOutput = ["status" => true, "message" => __("Mobile number updated successfully.")];
                    return $this->jsonHelper->jsonEncode($jsonOutput);
                }


            }else{
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Customer does not exist.")]);
            }

            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

	/**
     * {@inheritdoc}
     */
    public function getLogin($mobileEmail, $password, $websiteId)
    {
        try {
            if (empty($mobileEmail) || empty($password) || empty($websiteId)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = [];
            if (is_numeric($mobileEmail)) {
                $collection = $this->_helperdata->checkCustomerExists("+".$mobileEmail, "mobile", $websiteId);
            } else {
                $collection = $this->_helperdata->checkCustomerExists($mobileEmail, "email", $websiteId);
            }

            if (count($collection) == 0) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Customer does not exists.")]);
            } else {
                $customerItem = $collection->getFirstItem();
                $customerId = $customerItem->getId();
            }

            if($this->customerAuthentication->authenticate($customerId, $password)) {
                $token = $this->tokenModelFactory->create()->createCustomerToken($customerId)->getToken();
                $response['status'] = true;
                $response['token'] = $token;
            } else {
                $response = ["status"=>false, "message"=>__("Customer does not exists.")];
            }
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }
	
    /**
     * {@inheritdoc}
     */
    public function getForgotPasswordOTP($mobileNumber, $websiteId)
    {
        try {
            if (empty($mobileNumber) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->sendForgotPasswordOTP(["forgotmob"=>$mobileNumber], $websiteId);
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getForgotPasswordOTPVerify($mobileNumber, $otp, $websiteId)
    {
        try {
            if (empty($mobileNumber) || empty($otp) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->verifyForgotPasswordOTP(
                ["mobile"=>$mobileNumber,"verifyotp"=>$otp],
                $websiteId
            );
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword($mobileNumber, $otp, $password, $websiteId)
    {
        try {
            if (empty($mobileNumber) || empty($otp) || empty($password) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $isVerified = $this->_helperdata->checkOTPisVerified(
                [
                    "mobile"=>$mobileNumber,
                    "otp"=>$otp,
                ],
                $this->_helperdata::FORGOTPASSWORD_OTP_TYPE,
                $websiteId
            );
            if (count($isVerified) == 1) {
                $response = $this->_helperdata->resetForgotPassword(
                    ["mobilenumber"=>$mobileNumber,"password"=>$password],
                    $websiteId
                );
            } else {
                $response = ["status"=>false, "message"=>__("Mobile no is not verified.")];
            }
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createAccountOTP($mobileNumber, $websiteId)
    {
        try {
            if (empty($mobileNumber) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->sendRegistrationOTP(["mobile"=>$mobileNumber], $websiteId);
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createAccountVerifyOTP($mobileNumber, $otp, $websiteId)
    {
        try {
            if (empty($mobileNumber) || empty($otp) || (empty($websiteId) && empty($websiteId)>0)) {
                return $this->jsonHelper->jsonEncode(["status"=>false, "message"=>__("Invalid parameter list.")]);
            }
            $response = $this->_helperdata->verifyRegistrationOTP(
                ["mobile"=>$mobileNumber, "verifyotp"=>$otp],
                $websiteId
            );
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    public function createAccount(CustomerInterface $customer, $mobileNumber, $otp, $password = null, $redirectUrl = '')
    {
        try {
            $isVerified = $this->_helperdata->checkOTPisVerified(
                [
                    "mobile"=>$mobileNumber,
                    "otp"=>$otp,
                ],
                $this->_helperdata::REGISTRATION_OTP_TYPE,
                $customer->getWebsiteId()
            );
            if (count($isVerified) == 1) {
                $collection = $this->_helperdata->checkCustomerExists(
                    $mobileNumber,
                    "mobile",
                    $customer->getWebsiteId()
                );
                if (count($collection) == 0) {
                    $customer->setCustomAttribute("mobilenumber", $mobileNumber);
                    $this->_helperdata->createAccount(
                        $customer,
                        ["password"=>$password],
                        "",
                        $customer->getWebsiteId()
                    );
                    $response = ["status"=>true, "message"=>__("Customer account created.")];
                } else {
                    $response = ["status"=>false, "message"=>__("Customer is already exists.")];
                }
            } else {
                $response = ["status"=>false, "message"=>__("Mobile no is not verified.")];
            }
            return $this->jsonHelper->jsonEncode($response);
        } catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }
}
