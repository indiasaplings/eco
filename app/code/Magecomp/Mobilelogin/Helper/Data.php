<?php
namespace Magecomp\Mobilelogin\Helper;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlFactory;

/**
 * Class Data
 * Magecomp\Mobilelogin\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION = 'mobilelogin/generalsettings/adminemailtemplate';
    const XML_PATH_EMAIL_ADMIN_QUOTE_SENDER = 'mobilelogin/generalsettings/adminemailsender';

    const MOBILELOGIN_MODULEOPTION_ENABLE = 'mobilelogin/moduleoption/enable';
    
    const MOBILELOGIN_GENERALSETTINGS_OTPTYPE = 'mobilelogin/generalsettings/otptype';
    const MOBILELOGIN_GENERALSETTINGS_OTP = 'mobilelogin/generalsettings/otp';
    const MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY = 'mobilelogin/generalsettings/loginnotify';
    const MOBILELOGIN_OTPSEND_MESSAGE = 'mobilelogin/otpsend/message';
    const MOBILELOGIN_UPDATEOTPSEND_MESSAGE = 'mobilelogin/updateotpsend/message';

    const MOBILELOGIN_DESIGN_TEMPLATEREG  =  'mobilelogin/design/templatereg';
    const MOBILELOGIN_DESIGN_TEMPLATELOGIN  =  'mobilelogin/design/templatelogin';
    const MOBILELOGIN_DESIGN_TEMPLATEFORGOT  =  'mobilelogin/design/templateforgot';
    const MOBILELOGIN_DESIGN_MAINLAYOUT    = 'mobilelogin/design/mainlayout';
    const MOBILELOGIN_DESIGN_LAYOUT  =    'mobilelogin/design/layout';
    const MOBILELOGIN_DESIGN_IMAGEREG = 'mobilelogin/design/imagereg';
    const MOBILELOGIN_DESIGN_IMAGELOGIN = 'mobilelogin/design/imagelogin';
    const MOBILELOGIN_DESIGN_IMAGEFORGOT = 'mobilelogin/design/imageforgot';
    const COUNTRY_CODE_PATH = 'general/country/default';
    const COUNTRY_CODE_ALLOW = 'general/country/allow';

    const REGISTRATION_OTP_TYPE = "REGISTER";
    const LOGIN_OTP_TYPE = "LOGIN";
    const FORGOTPASSWORD_OTP_TYPE = "FORGOT";

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\Http
     */
    protected $httpFile;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected $customerCollection;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var Apicall
     */
    protected $apicall;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;
    protected $objectManager;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\Driver\Http $httpFile
     * @param CustomerCollection $customerCollection
     * @param RemoteAddress $remoteAddress
     * @param Apicall $apicall
     * @param CustomerFactory $CustomerFactory
     * @param \Magecomp\Mobilelogin\Model\OtpFactory $otp
     * @param AccountManagementInterface $accountManagement
     * @param CustomerExtractor $customerExtractor
     * @param Session $customerSession
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param Escaper $escaper
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Driver\Http $httpFile,
        CustomerCollection $customerCollection,
        RemoteAddress $remoteAddress,
        Apicall $apicall,
        CustomerFactory $customerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magecomp\Mobilelogin\Model\OtpFactory $otp,
        \Magecomp\Mobilelogin\Model\ResourceModel\Otp\CollectionFactory $otpCollection,
        AccountManagementInterface $accountManagement,
        CustomerExtractor $customerExtractor,
        Session $customerSession,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        Escaper $escaper,
        TimezoneInterface $timezoneInterface,
        UrlFactory $urlFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->httpFile = $httpFile;
        $this->customerCollection = $customerCollection;
        $this->objectManager = $objectManager;
        $this->remoteAddress = $remoteAddress;
        $this->apicall = $apicall;
        $this->_customerFactory = $customerFactory;
        $this->_otpModal = $otp;
        $this->_otpCollection = $otpCollection;
        $this->accountManagement = $accountManagement;
        $this->customerExtractor = $customerExtractor;
        $this->session = $customerSession;
        $this->_encryptor = $encryptor;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->escaper = $escaper;
        $this->timezoneInterface = $timezoneInterface;
        $this->urlFactory = $urlFactory;
    }

    /**
     * @return mixed
     */
    public function isMobileEnable()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_MODULEOPTION_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getGeoCountryCode()
    {
        try {
            $ipData = $this->jsonHelper->jsonDecode(
                $this->httpFile->fileGetContents(
                    "www.geoplugin.net/json.gp?ip=".$this->remoteAddress->getRemoteAddress()
                )
            );
            return $ipData->geoplugin_countryCode;
        } catch (\Exception $e) {
            return $this->scopeConfig->getValue(
                self::COUNTRY_CODE_PATH,
                ScopeInterface::SCOPE_WEBSITES
            );
        }
    }

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_PATH,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @return mixed
     */
    public function getAllowCountry()
    {
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_ALLOW,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @param bool $isArray
     * @return array|string
     */
    public function getApplicableCountry($isArray = true)
    {
        $_defaultCountry = [];
        $_country = [];
        $_defaultCountry[] = $this->getDefaultCountry();
        $_country = array_merge($_defaultCountry, explode(",", $this->getAllowCountry()));
        if (!$isArray) {
            $_country = $this->jsonHelper->jsonEncode($_country);
        }
        return $_country;
    }
        
    /**
     * @return bool
     */
    public function isEnable()
    {
        if ($this->isMobileEnable()) {
                $geoCountryCode = $this->getGeoCountryCode();
            if (!in_array($geoCountryCode, $this->getApplicableCountry())) {
                return false;
            }
                return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getLayoutType()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout == 'ultimatelayout') {
            return 1;
        }
    }

    /**
     * @return mixed
     */
    public function getOtpStringlenght()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed|string
     */
    public function getRegTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }
        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATEREG,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGEREG,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }
    public function getLoginTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }
        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATELOGIN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGELOGIN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }
    public function getForgotTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }
        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATEFORGOT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGEFORGOT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }

    /**
     * @return int
     */
    public function getImageType()
    {
        $imageType = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($imageType == 'template') {
            return 1;
        } else {
            return 0;
        }
    }

    /***
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @param bool $fromStore
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * @return mixed
     */
    public function isEnableLoginEmail()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $remoteId
     * @param $mail
     * @param $userAgent
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMail($remoteId, $mail, $userAgent)
    {
        // Send Mail To Admin For This
        $date = $this->timezoneInterface->date()->format('Y-m-d H:i:s');

        $browser = $this->get_browser_name($userAgent);
            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->transportBuilder
               ->setTemplateIdentifier(
                   $this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION, $storeScope)
               )
               ->setTemplateOptions(
                   [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
               )
               ->setTemplateVars([
                    'ip'  => $remoteId,
                    'email' => $mail,
                    'datetime' => $date,
                    'browser' => $browser
                ])
               ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_SENDER, $storeScope))
               ->addTo($mail)
               ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return "true";
    }

    /**
     * @return mixed
     */
    public function getOtpStringtype()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTPTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getRegOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_OTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return false|string
     */
    public function generateRandomString()
    {
        $length = $this->getOtpStringlenght();
        if ($this->getOtpStringtype() == "N") {
            $randomString = substr(str_shuffle("0123456789"), 0, $length);
        } else {
            $randomString = substr(
                str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),
                0,
                $length
            );
        }
        return $randomString;
    }

    /**
     * @param $mobile
     * @param $randomCode
     * @return mixed|string|string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRegOtpMessage($mobile, $randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = ['{{shop_name}}','{{shop_url}}','{{random_code}}'];
        $accurate = [$storeName,$storeUrl,$randomCode];
        return str_replace($codes, $accurate, $this->getRegOtpTemplate());
    }

    public function getAfterLoginRedirect()
    {
        $redirectOption = $this->scopeConfig->getValue(
            "customer/startup/redirect_dashboard",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($redirectOption == 1) {
            return $this->urlFactory->create()->getUrl('customer/account/');
        }
        return "";
    }

    /**
     * @param $fieldValue
     * @param string $fieldType
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkCustomerExists($fieldValue, $fieldType = "mobile", $websiteId = 1)
    {
        $collection = $this->_customerFactory->create()->getCollection();
        if ($fieldType == "mobile") {
            $collection->addFieldToFilter("mobilenumber", $fieldValue);
        }
        if ($fieldType == "email") {
            $collection->addFieldToFilter("email", $fieldValue);
        }
        $collection->addAttributeToFilter('website_id', $websiteId);
        return $collection;
    }

    /**
     * @param $mobile
     * @param int $websiteId
     * @return bool
     */
    public function checkCustomerWithSameMobileNo($mobile, $websiteId = 1)
    {
        $customer = $this->checkCustomerExists($mobile, "mobile", $websiteId);
        if (count($customer) > 0) {
            return true;
        }
        return false;
    }

    public function sendUpdateOTPCode($data, $websiteId = 1)
    {

        try {
            if ($this->checkCustomerWithSameMobileNo($data, $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }
            $randomCode = $this->generateRandomString();
            $message = $this->getUpdateOtpMessage($data, $randomCode);

            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data, self::REGISTRATION_OTP_TYPE, $websiteId);

            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }
            $otpModel->setType(self::REGISTRATION_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->curlApiCall($message, $data);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }

    }

    /**
     * @param $randomCode
     * @return string
     */
    public function encryptOtp($randomCode)
    {
        return $this->_encryptor->encrypt($randomCode);
    }

    /**
     * @param $randomCode
     * @return string
     */
    public function decryptOtp($randomCode)
    {
        return $this->_encryptor->decrypt($randomCode);
    }

    /**
     * @param $mobile
     * @param string $type
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkOTPExists($mobile, $type = "", $websiteId = 1)
    {
        return $this->_otpCollection->create()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('mobile', $mobile)
            ->addFieldToFilter('is_verify', 0)
            ->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $data
     * @param string $type
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkOTPisVerified($data, $type = "", $websiteId = 1)
    {
        return $this->_otpCollection->create()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('mobile', $data['mobile'])
            ->addFieldToFilter('random_code', $data['otp'])
            ->addFieldToFilter('is_verify', 1)
            ->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $message
     * @param $mobilenumbers
     * @return array|bool[]|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function curlApiCall($message, $mobilenumbers)
    {
        return $this->apicall->curlApiCall($message, $mobilenumbers);
    }

    public function getUpdateOtpMessage($mobile,$randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = array('{{shop_name}}','{{shop_url}}','{{random_code}}');
        $accurate = array($storeName,$storeUrl,$randomCode);
        return str_replace($codes,$accurate,$this->getUpdateOtpTemplate());
    }
     public function getUpdateOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_UPDATEOTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]|string
     */
    public function sendRegistrationOTP($data, $websiteId = 1)
    {
         try {
            if ($this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }
            $randomCode = $this->generateRandomString();
            $message = $this->getRegOtpMessage($data['mobile'], $randomCode);
            
            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data['mobile'], self::REGISTRATION_OTP_TYPE, $websiteId);
            
            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }
            $otpModel->setType(self::REGISTRATION_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['mobile']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->curlApiCall($message, $data['mobile']);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyRegistrationOTP($data, $websiteId = 1)
    {
        try {
            if ($this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }
            
            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::REGISTRATION_OTP_TYPE)
                ->addFieldToFilter('website_id', $websiteId);


            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }
            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $customer
     * @param $data
     * @param $redirectUrl
     * @param $websiteId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function createAccount($customer, $data, $redirectUrl, $websiteId)
    {
        try {
            $customer = $this->accountManagement
                    ->createAccount($customer, $data['password'], $redirectUrl);
            return $customer;
        } catch (\Exception $e) {
            throw new AuthenticationException(
                __('There is some issue. Please try after some time.')
            );
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]|string
     */
    public function sendLoginOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['loginotpmob'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $randomCode = $this->generateRandomString();
            $message = $this->getRegOtpMessage($data['loginotpmob'], $randomCode);

            $otpModel = $this->_otpModal->create();
            $collection = $this->checkOTPExists($data['loginotpmob'], self::LOGIN_OTP_TYPE, $websiteId);
            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }
            $otpModel->setType(self::LOGIN_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['loginotpmob']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->curlApiCall($message, $data['loginotpmob']);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyLoginOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("ss Customer does not exists.")];
            }
            
            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::LOGIN_OTP_TYPE)
                ->addFieldToFilter('is_verify', 0)
                ->addFieldToFilter('website_id', $websiteId);

            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }
            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
    public function sendOTPCode($mobile,$websiteId = 1)
    {
       try {
            if ($this->checkCustomerWithSameMobileNo($mobile, $websiteId)) {
                return ["status"=>"exist", "message"=>__("Customer already exists.")];
            }
            
            $otpModels = $this->_otpModal->create();      
        $collection = $otpModels->getCollection();
        $collection->addFieldToFilter('mobile', $mobile);
        
        $objDate = $this->objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $randomCode = $this->generateRandomString();
        $message = $this->getRegOtpMessage($mobile,$randomCode);
        
        if(count($collection) == 0){
        
            $otpModel = $this->_otpModal->create();
            $otpModel->setRandomCode($randomCode);
            $otpModel->setCreatedTime($date);   
            $otpModel->setIsVerify(0);  
            $otpModel->setMobile($mobile);  
            $otpModel->save();  
        }else{
            
            $otpModel = $this->_otpModal->create()->load($mobile,'mobile');
            $otpModel->setRandomCode($randomCode);
            $otpModel->setCreatedTime($date);   
            $otpModel->setIsVerify(0);  
            $otpModel->setMobile($mobile);
            $otpModel->save();      
        }
        $apiReturn = $this->curlApiCall($message,$mobile);
        return $apiReturn;
    
        }catch(\Exception $e)
        {
           return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param $websiteId
     * @return array|bool[]|string
     */
    public function sendForgotPasswordOTP($data, $websiteId)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['forgotmob'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }
            
            $randomCode = $this->generateRandomString();
            $message = $this->getRegOtpMessage($data['forgotmob'], $randomCode);

            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data['forgotmob'], self::FORGOTPASSWORD_OTP_TYPE, $websiteId);
            
            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }
            $otpModel->setType(self::FORGOTPASSWORD_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['forgotmob']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->curlApiCall($message, $data['forgotmob']);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /***
     * @param $data
     * @param int $websiteId
     * @return array
     */
    public function sendForgotPasswordEmail($data, $websiteId = 1)
    {
        try {
            if (!\Zend_Validate::is($data['email'], \Magento\Framework\Validator\EmailAddress::class)) {
                $this->session->setForgottenEmail($data['email']);
                return ["status"=>false, "message"=>__("Please enter correct email address.")];
            }
            
            if (count($this->checkCustomerExists($data['email'], "email", $websiteId)) <= 0) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }
            
            $this->accountManagement->initiatePasswordReset(
                $data['email'],
                AccountManagement::EMAIL_RESET
            );
            return ["status"=>true, "message"=>
                __('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->escaper->escapeHtml($data['email']))];
        } catch (NoSuchEntityException $exception) {
            return ["status"=>false, "message"=> $exception->getMessage()];
        } catch (SecurityViolationException $exception) {
            return ["status"=>false, "message"=> $exception->getMessage()];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>'We\'re unable to send the password reset email.'];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyForgotPasswordOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }
            
            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::FORGOTPASSWORD_OTP_TYPE)
                ->addFieldToFilter('website_id', $websiteId);

            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }
            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }


    /**
     * @param $data
     * @param int $websiteId
     * @return array
     */
    public function resetForgotPassword($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }
            
            $collection = $this->_customerFactory->create()->getCollection()
                ->addFieldToFilter("mobilenumber", $data['mobile']);
            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $customer = $this->_customerFactory->create();
                $customer = $customer->setWebsiteId($websiteId);
                $customer = $customer->loadByEmail($item->getEmail());
                $customer->setRpToken($item->getRpToken());
                $customer->setPassword($data['password']);

                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('mobilenumber', $data['mobile']);
                $customer->updateData($customerData);
                $customer->save();
                return ["status"=>true, "message"=>__("Your password changed successfully.")];
            }
            return ["status"=>false, "message"=>__("Customer does not exists.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
}
