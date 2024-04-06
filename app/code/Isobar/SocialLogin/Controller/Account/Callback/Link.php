<?php
namespace Isobar\SocialLogin\Controller\Account\Callback;

use Isobar\SocialLogin\Exception\InvalidSocialAccountException;
use Isobar\SocialLogin\Model\Provider\Account\ConverterInterface;
use Isobar\SocialLogin\Api\AccountRepositoryInterface;
use Isobar\SocialLogin\Controller\Account\Callback;
use Isobar\SocialLogin\Helper\State;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Link
 */
class Link extends Callback
{
    /**
     * @var AccountRepositoryInterface
     */
    protected $accountRepository;

    /**
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Isobar\SocialLogin\Model\Config\General $generalConfig
     * @param \Isobar\SocialLogin\Model\ProviderManagement $providerManagement
     * @param State $stateHelper
     * @param AccountRepositoryInterface $accountRepository
     * @param ConverterInterface $converter
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Isobar\SocialLogin\Model\Config\General $generalConfig,
        \Isobar\SocialLogin\Model\ProviderManagement $providerManagement,
        State $stateHelper,
        AccountRepositoryInterface $accountRepository,
        ConverterInterface $converter,
        CustomerSession $customerSession
    ) {
        parent::__construct(
            $context,
            $logger,
            $generalConfig,
            $providerManagement,
            $stateHelper
        );
        $this->accountRepository = $accountRepository;
        $this->converter = $converter;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $response = $this->resultRedirectFactory->create()->setPath('social/customer/accounts');

        try {
            $providerAccount = $this->stateHelper->getAccount();

            $account = $this->converter->convert($providerAccount);
            $account->setCustomerId($this->customerSession->getCustomerId());
            $this->accountRepository->save($account);

            $this->stateHelper->clear();
        } catch (InvalidSocialAccountException $e) {
            $this->messageManager->addErrorMessage(__('Social account already taken'));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $this->getRequest()->getParams());
            $this->messageManager->addErrorMessage(__('Social account already exists.'));
        }

        return $response;
    }
}
