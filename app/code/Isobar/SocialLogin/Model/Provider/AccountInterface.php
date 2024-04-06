<?php
namespace Isobar\SocialLogin\Model\Provider;

/**
 * Interface AccountInterface
 */
interface AccountInterface
{
    /**#@+
     * Account data fields
     */
    const TYPE = 'type';

    const FIRST_NAME = 'first_name';

    const LAST_NAME = 'last_name';

    const EMAIL = 'email';

    const IMAGE = 'image';

    const SOCIAL_ID = 'social_id';
    /**#@-*/

    /**#@+
     * Account types
     */
    const TYPE_KAKAO = 'kakao';
    const TYPE_NAVER = 'naver';
    const TYPE_LINE = 'line';

    /**#@-*/

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $name
     * @return $this
     */
    public function setFirstName($name);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $name
     * @return $this
     */
    public function setLastName($name);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $imageUrl
     * @return $this
     */
    public function setImage($imageUrl);

    /**
     * @return string
     */
    public function getSocialId();

    /**
     * @param string $socialId
     * @return $this
     */
    public function setSocialId($socialId);

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data);
}
