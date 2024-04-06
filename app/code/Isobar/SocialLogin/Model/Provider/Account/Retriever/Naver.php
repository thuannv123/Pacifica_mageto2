<?php


namespace Isobar\SocialLogin\Model\Provider\Account\Retriever;

use Isobar\SocialLogin\Model\Provider\Account\AbstractRetriever;
use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;
use Magento\Framework\DataObject;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Token\Exception\ExpiredTokenException;

class Naver extends AbstractRetriever
{
    /**
     * Get account method
     */
    const API_METHOD_ACCOUNT_GET = '/nid/me';

    /**
     * @param ServiceInterface $service
     * @return DataObject|mixed
     * @throws Exception
     * @throws TokenNotFoundException
     * @throws ExpiredTokenException
     */
    protected function requestData(ServiceInterface $service)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Naver $service */
        $response = $service->requestWithParams(self::API_METHOD_ACCOUNT_GET, []);

        $responseData = $this->decodeJson($response);
        return $this->createDataObject()->setData($responseData);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareResponseData(DataObject $responseData)
    {
        $data =  $responseData->getData('response');
        return [
            AccountInterface::TYPE => AccountInterface::TYPE_NAVER,
            AccountInterface::SOCIAL_ID => $data['id'],
            AccountInterface::FIRST_NAME => isset($data['name']) ? $data['name'] : (isset($data['nickname']) ? $data['nickname'] : ''),
            AccountInterface::IMAGE => isset($data['profile_image']) ? $data['profile_image'] : '',
            AccountInterface::EMAIL => isset($data['email']) ? $data['email'] : '',
        ];
    }
}
