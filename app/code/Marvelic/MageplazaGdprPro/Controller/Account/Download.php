<?php

namespace Marvelic\MageplazaGdprPro\Controller\Account;

use Mageplaza\GdprPro\Controller\Account\Download as GdprProAccountDownload;

class Download extends GdprProAccountDownload
{
    /**
     * @param array $addressData
     * @param bool $checkXmlFile
     *
     * @return array
     */
    public function formatAddressData($addressData, $checkXmlFile = false)
    {
        $arr = [];
        if (is_array($addressData)) {
            foreach ($addressData as $key => $value) {
                if (!is_array($value)) {
                    if ($value !== null) {
                        $key  = str_replace('_', ' ', $key);
                        $data = ('Address ' . ucfirst($key) . ',' . $value);
                        if ($checkXmlFile) {
                            $arr [] = $data;
                        } else {
                            $arr [] = [$data];
                        }
                    }
                } elseif($key == 'custom_attributes') {
                    foreach($value as $subArray) {
                        $data = ('Address ' . ucfirst($subArray['attribute_code']) . ',' . $subArray['value']);
                    }
                    if ($checkXmlFile) {
                        $arr [] = $data;
                    } else {
                        $arr [] = [$data];
                    }
                } else {
                    $data = ('Address ' . ucfirst($key) . ',' . implode(',', $value));
                    if ($checkXmlFile) {
                        $arr [] = $data;
                    } else {
                        $arr [] = [$data];
                    }
                }
            }
        }

        return $arr;
    }
}
