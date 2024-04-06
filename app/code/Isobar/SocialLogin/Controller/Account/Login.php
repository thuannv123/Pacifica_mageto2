<?php
namespace Isobar\SocialLogin\Controller\Account;

use Isobar\SocialLogin\Helper\State;

/**
 * Class Login
 */
class Login extends AbstractLogin
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->stateHelper->setState(State::STATE_LOGIN);
        return parent::execute();
    }
}
