<?php
namespace Isobar\SocialLogin\Model\Provider;

use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

/**
 * Interface ServiceBuilderInterface
 */
interface ServiceBuilderInterface
{
    /**
     * @return ServiceInterface
     */
    public function build();
}
