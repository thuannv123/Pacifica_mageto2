<?php
namespace Isobar\SocialLogin\Helper;

use Isobar\SocialLogin\Exception\InvalidStateException;
use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Magento\Customer\Model\Session;

/**
 * Class State
 */
class State
{
    /**#@+
     * States
     */
    const STATE_LOGIN = 'login';
    const STATE_LINK = 'link';
    /**#@-*/

    /**#@+
     * Session keys
     */
    const SESSION_KEY_STATE = 'social_login_state';
    const SESSION_KEY_ACCOUNT = 'social_login_account';
    /**#@-*/

    /**
     * @var Session
     */
    protected $session;

    /**
     * States
     * @var array
     */
    protected $states = [
        self::STATE_LOGIN,
        self::STATE_LINK
    ];

    /**
     * Default state
     *
     * @var string
     */
    protected $defaultState = self::STATE_LOGIN;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Set account
     *
     * @param AccountInterface $account
     * @return $this
     */
    public function setAccount(AccountInterface $account)
    {
        $this->session->setData(self::SESSION_KEY_ACCOUNT, $account);
        return $this;
    }

    /**
     * Get account
     *
     * @return AccountInterface|null
     * @throws InvalidStateException
     */
    public function getAccount()
    {
        $account = $this->_getAccount();
        if (!$account instanceof AccountInterface) {
            throw new InvalidStateException(__('Invalid provider account'));
        }
        return $account;
    }

    /**
     * Get account data
     *
     * @return AccountInterface | null
     */
    protected function _getAccount()
    {
        return $this->session->getData(self::SESSION_KEY_ACCOUNT);
    }

    /**
     * Is account exist
     *
     * @return bool
     */
    public function isAccountExist()
    {
        return $this->_getAccount() !== null;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $state = $this->session->getData(self::SESSION_KEY_STATE);
        if (!$this->isValidState($state)) {
            $state = $this->defaultState;
        }

        return $state;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return $this
     * @throws InvalidStateException
     */
    public function setState($state)
    {
        if (!$this->isValidState($state)) {
            throw new InvalidStateException(__('State %1 invalid', $state));
        }

        $this->session->setData(self::SESSION_KEY_STATE, $state);

        return $this;
    }

    /**
     * Is valid state
     *
     * @param string $state
     * @return bool
     */
    public function isValidState($state)
    {
        return in_array($state, $this->states);
    }

    /**
     * Clear state
     *
     * @throws InvalidStateException
     */
    public function clear()
    {
        $this->session->setData(self::SESSION_KEY_STATE, null);
        $this->session->setData(self::SESSION_KEY_ACCOUNT, null);
    }
}
