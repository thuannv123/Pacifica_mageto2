<?php
namespace Marvelic\SocialLogin\Api\Data;

/**
 * Account interface
 */
interface AccountInterface
{
    /**#@+
     * Account data fields
     */
    const ACCOUNT_ID = 'social_customer_id';
    const SOCIAL_ID = 'social_id';
    const CUSTOMER_ID = 'customer_id';
    const TYPE = 'type';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getSocialId();

    /**
     * @param string $socialId
     * @return $this
     */
    public function setSocialId($socialId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer();

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer);
}
