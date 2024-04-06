<?php
namespace Anowave\Ec\vendor\Google\Logger;

use Anowave\Ec\vendor\Google\Logger\AbstractLogger as Google_Logger_Abstract;

class Nil extends Google_Logger_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function shouldHandle($level)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function write($message, array $context = array())
    {
    }
}