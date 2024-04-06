<?php
namespace Anowave\Ec\vendor\Google\Verifier;

abstract class AbstractVerifier
{
	/**
	* Checks a signature, returns true if the signature is correct, false otherwise.
	*/
  	abstract public function verify($data, $signature);
}