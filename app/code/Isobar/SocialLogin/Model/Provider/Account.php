<?php

namespace Isobar\SocialLogin\Model\Provider;

use Magento\Framework\DataObject;

class Account extends DataObject implements AccountInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($name)
    {
        $this->setData(self::FIRST_NAME, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($name)
    {
        $this->setData(self::LAST_NAME, $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($imageUrl)
    {
        $this->setData(self::IMAGE, $imageUrl);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialId()
    {
        return $this->getData(self::SOCIAL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialId($socialId)
    {
        $this->setData(self::SOCIAL_ID, $socialId);
        return $this;
    }
}
