<?php

namespace Atome\MagentoPayment\Services\Config;

class ConfigService
{

    public function getNewUserOffImage()
    {
        $image = Atome::getNewUserOffImage();
        if ($image) {
            return Atome::PLUGIN_HOST . '/plugins/common/assets/svg/' . $image;
        }

        return null;
    }


}
