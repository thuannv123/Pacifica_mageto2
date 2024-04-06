<?php
namespace Isobar\SocialLogin\Model\Provider\Account\Retriever;

use Isobar\SocialLogin\Model\Provider\Account\AbstractRetriever;
use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

class Kakao extends AbstractRetriever
{
    /**
     * Get account method
     */
    const API_METHOD_ACCOUNT_GET = '/user/me';

    /**
     * {@inheritdoc}
     */
    protected function requestData(ServiceInterface $service)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Kakao $service */
        $response = $service->request(self::API_METHOD_ACCOUNT_GET);
        $responseData = $this->decodeJson($response);
        return $this->createDataObject()->setData($responseData);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareResponseData(\Magento\Framework\DataObject $responseData)
    {
        $data = $responseData->getData();
        return [
                AccountInterface::TYPE => AccountInterface::TYPE_KAKAO,
                AccountInterface::SOCIAL_ID => $responseData->getData('id'),
                AccountInterface::FIRST_NAME => isset($data['kakao_account']['profile']['nickname']) ? $data['kakao_account']['profile']['nickname'] : '',
                AccountInterface::IMAGE => isset($data['kakao_account']['profile']['thumbnail_image_url']) ? $data['kakao_account']['profile']['thumbnail_image_url'] : '',
                AccountInterface::EMAIL => isset($data['kakao_account']['email']) ? $data['kakao_account']['email'] : ''
            ];
    }
}
