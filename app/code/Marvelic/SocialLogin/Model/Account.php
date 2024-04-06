<?php
namespace Marvelic\SocialLogin\Model;

use Marvelic\SocialLogin\Api\Data\AccountInterface;
use Marvelic\SocialLogin\Model\ResourceModel\Account as AccountResource;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Account
 */
class Account extends AbstractModel implements AccountInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(AccountResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(self::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ACCOUNT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialId()
    {
        return $this->_getData(self::SOCIAL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialId($socialId)
    {
        return $this->setData(self::SOCIAL_ID, $socialId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customerRepository->getById($this->getCustomerId());
    }
    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer)
    {
        return $this->setCustomerId($customer->getId());
    }
}
