<?php

namespace Atome\MagentoPayment\Services\Payment\API;


class Response
{
    protected $_data;

    public function setData($data)
    {
        $this->_data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) === 'get') {
            $k = lcfirst(substr($method, 3));
            return $this->_data[$k] ?? null;
        }

        throw new \RuntimeException('unknown method call: ' . $method);
    }
}
