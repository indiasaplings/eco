<?php
namespace Magecomp\Mobilelogin\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;

/**
 * Class Apicall
 * Magecomp\Mobilelogin\Helper
 */
class Apicall extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * Apicall constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Curl $curl
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->curl = $curl;
    }

    /**
     * @return mixed
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/moduleoption/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getAuthkey()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/general/authkey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getRouttype()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/general/routtype',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/general/password',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/general/apiurl',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->scopeConfig->getValue(
            'mobilelogin/general/senderid',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $message
     * @param $mobilenumbers
     * @return string
     * @throws LocalizedException
     */
    public function curlApiCall($message, $mobileNumbers)
    {
        try {
            if ($this->isEnable()) {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info($message);
return ["status"=>true, "message"=> ""];
                $postData = [
                    'authkey' => $this->getAuthkey(),
                    'mobiles' => $mobileNumbers,
                    'message' => urlencode($message),
                    'sender' => $this->getSenderId(),
                    'route' => $this->getRouttype()
                ];

                $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, 2);

                $this->curl->post($this->getApiUrl(), $postData);
                $response = $this->curl->getBody();

                if (is_array($response) && isset($response['status']) && $response['status'] == 200) {
                    $return = ["status"=>true];
                } else {
                    $return = ["status"=>false, "message"=> __("Something is wrong. Please try again later.")];
                }
            }
        } catch (Exception $e) {
            $return = ["status"=>false, "message"=> $e->getMessage()];
        }
        
    }
}
