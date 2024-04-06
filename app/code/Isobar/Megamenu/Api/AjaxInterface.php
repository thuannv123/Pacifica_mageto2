<?php
namespace Isobar\Megamenu\Api;

interface AjaxInterface
{

    /**
     * @param string $param
     * @return array
     */
    public function getMenuMobile($param);

}
