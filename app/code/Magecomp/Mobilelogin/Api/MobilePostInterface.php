<?php
namespace Magecomp\Mobilelogin\Api;

/**
 * Interface MobilePostInterface
 * Magecomp\Mobilelogin\Api
 */
interface MobilePostInterface
{
    /**
     * Generate login OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param int $websiteId
     * @return string
     */
    public function getLoginOTP($mobileNumber, $websiteId);

    /**
     * Verify login OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param string $otp
     * @param int $websiteId
     * @return string
     */
    public function getLoginOTPVerify($mobileNumber, $otp, $websiteId);

	/**
     * Do Login via Mobile or Email
     *
     * @api
     * @param string $mobileEmail
     * @param string $password
     * @param int $websiteId
     * @return mixed
     */
    public function getLogin($mobileEmail, $password, $websiteId);
	
    /**
     * Generate forgot password OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param int $websiteId
     * @return string
     */
    public function getForgotPasswordOTP($mobileNumber, $websiteId);

    /**
     * Verify forgot password OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param string $otp
     * @param int $websiteId
     * @return string
     */
    public function getForgotPasswordOTPVerify($mobileNumber, $otp, $websiteId);
    /**
     * Generate update number OTP for customer
     *
     * @api
     * @param string $newMobileNumber
     * @param int $websiteId
     * @param int $customerId
     * @param string $oldMobileNumber
     * @return string
     */
    public function getApiUpdateOTPCode($newMobileNumber, $websiteId, $customerId, $oldMobileNumber);

    /**
     * Verify update password OTP for customer
     *
     * @api
     * @param string $newMobileNumber
     * @param string $otp
     * @param int $customerId
     * @param string $oldMobileNumber
     * * @param string $websiteId
     * @return string
     */
    public function updateNumberVerifyOTP($newMobileNumber, $otp, $customerId, $oldMobileNumber, $websiteId);

    /**
     * Update customer password
     *
     * @api
     * @param string $mobileNumber
     * @param string $otp
     * @param string $password
     * @param int $websiteId
     * @return string
     */
    public function resetPassword($mobileNumber, $otp, $password, $websiteId);

    /**
     * Generate create account OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param int $websiteId
     * @return string
     */
    public function createAccountOTP($mobileNumber, $websiteId);

    /**
     * Verify create account OTP for customer
     *
     * @api
     * @param string $mobileNumber
     * @param string $otp
     * @param int $websiteId
     * @return string
     */
    public function createAccountVerifyOTP($mobileNumber, $otp, $websiteId);

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $mobileNumber
     * @param string $otp
     * @param string $password
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccount(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $mobileNumber,
        $otp,
        $password = null,
        $redirectUrl = ''
    );
}
