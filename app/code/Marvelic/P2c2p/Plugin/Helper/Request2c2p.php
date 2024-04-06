<?php

/*
 * Created by 2C2P
 * Date 19 June 2017
 * P2c2pRequest helper class is used to generate the current user request and send it to 2c2p payment getaway.
 */
namespace Marvelic\P2c2p\Plugin\Helper;


class Request2c2p {
	//This function is used to genereate the request for make payment to payment getaway.
	public function beforeP2c2p_construct_request($subject,$parameter,$isLoggedIn) {
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/p2c2p.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($parameter));
		
		return [$parameter,$isLoggedIn];
	}

}