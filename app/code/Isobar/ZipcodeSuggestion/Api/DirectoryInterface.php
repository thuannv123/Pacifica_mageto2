<?php

namespace Isobar\ZipcodeSuggestion\Api;

/**
 * Interface DirectoryInterface
 * @package Isobar\ZipcodeSuggestion\Api
 */
interface DirectoryInterface
{
    /**
     * Get Data ZipCode.
     *
     * @param string $data
     * @return string
     */
    public function getDataZipCode($data);
}
