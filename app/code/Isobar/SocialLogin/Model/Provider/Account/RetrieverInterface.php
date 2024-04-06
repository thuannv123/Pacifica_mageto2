<?php
namespace Isobar\SocialLogin\Model\Provider\Account;

use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

interface RetrieverInterface
{
    /**
     * @param ServiceInterface $service
     * @return AccountInterface
     */
    public function retrieve(ServiceInterface $service);
}
