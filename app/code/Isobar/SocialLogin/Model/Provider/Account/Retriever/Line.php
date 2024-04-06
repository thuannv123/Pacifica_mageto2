<?php
namespace Isobar\SocialLogin\Model\Provider\Account\Retriever;

use Isobar\SocialLogin\Model\Provider\Account\AbstractRetriever;
use Isobar\SocialLogin\Model\Provider\AccountInterface;
use Isobar\SocialLogin\Model\Provider\Service\ServiceInterface;

class Line extends AbstractRetriever
{
    /**
     * Get account method
     */
    const API_METHOD_ACCOUNT_GET = '/verify';

    /**
     * {@inheritdoc}
     */
    protected function requestData(ServiceInterface $service)
    {
        /** @var \Isobar\SocialLogin\Model\Provider\Service\Line $service */
        $response = $service->request(self::API_METHOD_ACCOUNT_GET, 'POST');
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
                AccountInterface::TYPE => AccountInterface::TYPE_LINE,
                AccountInterface::SOCIAL_ID => $data['sub'],
                AccountInterface::FIRST_NAME => isset($data['name']) ? $data['name'] : '',
                AccountInterface::IMAGE => isset($data['picture']) ? $data['picture'] : '',
                AccountInterface::EMAIL => isset($data['email']) ? $data['email'] : ''
            ];
    }
}
