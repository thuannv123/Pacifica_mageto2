<?php
namespace Isobar\SocialLogin\Model\Account;

use Isobar\SocialLogin\Api\Data\AccountInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class ImageProvider
 */
class ImageProvider
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var string
     */
    private $placeholderImage;

    /**
     * @param AssetRepository $assetRepository
     * @param string $placeholderImage
     */
    public function __construct(
        AssetRepository $assetRepository,
        $placeholderImage = 'Isobar_SocialLogin::images/user_placeholder.png'
    ) {
        $this->assetRepository = $assetRepository;
        $this->placeholderImage = $placeholderImage;
    }

    /**
     * Get account image url.
     *
     * @param AccountInterface $account
     * @return string
     */
    public function getAccountImageUrl(AccountInterface $account)
    {
        $url = $account->getImage();

        if (empty($url)) {
            $url = $this->getPlaceholderImageUrl();
        }

        return $url;
    }

    /**
     * Get placeholder image url.
     *
     * @return string
     */
    public function getPlaceholderImageUrl()
    {
        return $this->assetRepository->getUrl($this->placeholderImage);
    }
}
