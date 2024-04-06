<?php

namespace Isobar\SocialLogin\Model\Serialize;

/**
 * Class Serializer.
 *
 * Wrapper for hiding differences between serialization strategies in 2.1.* and 2.2.* versions magento.
 */
class Serializer
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Serialize data into string
     *
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function serialize($data)
    {
        if ($serializer = $this->getSerializer()) {
            return $serializer->serialize($data);
        }

        return serialize($data);
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserialize($string)
    {
        if ($serializer = $this->getSerializer()) {
            return $serializer->unserialize($string);
        }

        return unserialize($string);
    }

    /**
     * Get serializer.
     *
     * Get serializer if \Magento\Framework\Serialize\SerializerInterface exist.
     * If interface not exist return null.
     *
     * @return mixed
     */
    private function getSerializer()
    {
        $serializerInterfaceName = '\Magento\Framework\Serialize\SerializerInterface';
        if (!interface_exists($serializerInterfaceName)) {
            return null;
        }

        return $this->serializer;
    }
}
