<?php

namespace Isobar\SocialLogin\Model\Provider\Service\Credentials;

interface AdditionalCredentialsInterface extends CredentialsInterface
{
    /**
     * Get application public key.
     *
     * @return string
     */
    public function getPublicKey();
}
