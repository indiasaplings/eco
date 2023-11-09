<?php
namespace Magecomp\Mobilelogin\Block;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Mobilelogin
 * Magecomp\Mobilelogin\Block
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magecomp\Mobilelogin\Helper\Data
     */
    protected $_helper;

    /**
     * Mobilelogin constructor.
     * @param Context $context
     * @param \Magecomp\Mobilelogin\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magecomp\Mobilelogin\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getLayoutType()
    {
        return $this->_helper->getLayoutType();
    }

    /**
     * @return mixed
     */
    public function getOtpStringlenght()
    {
        return $this->_helper->getOtpStringlenght();
    }

    /**
     * @return mixed
     */
    public function getRegTemplateImage()
    {
        return $this->_helper->getRegTemplateImage();
    }
    public function getLoginTemplateImage()
    {
        return $this->_helper->getLoginTemplateImage();
    }
    public function getForgotTemplateImage()
    {
        return $this->_helper->getForgotTemplateImage();
    }

    /**
     * @return mixed
     */
    public function getImageType()
    {
        return $this->_helper->getImageType();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    /**
     * @return mixed
     */
    public function isEnable()
    {
        return $this->_helper->isEnable();
    }

    /**
     * @return mixed
     */
    public function isMobileEnable()
    {
        return $this->_helper->isMobileEnable();
    }

    /**
     * @return mixed
     */
    public function getGeoCountryCode()
    {
        return $this->_helper->getGeoCountryCode();
    }

    /**
     * @return mixed
     */
    public function getAllowCountry()
    {
        return $this->_helper->getAllowCountry();
    }

    /**
     * @return array|string
     */
    public function getApplicableCountry()
    {
        return $this->_helper->getApplicableCountry();
    }

    /**
     * @return array|string
     */
    public function getApplicableCountryJson()
    {
        return $this->_helper->getApplicableCountry(false);
    }
}
