<?php
namespace Isobar\SocialLogin\Controller\Account;

use Magento\Framework\Controller\ResultFactory;
use Isobar\SocialLogin\Controller\AbstractAction;

/**
 * Class Edit
 */
class Edit extends AbstractAction
{
    /**
     * @var
     */
    private $stateHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Isobar\SocialLogin\Model\Config\General $generalConfig
     * @param \Isobar\SocialLogin\Helper\State $stateHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Isobar\SocialLogin\Model\Config\General $generalConfig,
        \Isobar\SocialLogin\Helper\State $stateHelper
    ) {
        parent::__construct($context, $logger, $generalConfig);
        $this->stateHelper = $stateHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->stateHelper->isAccountExist()) {
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
