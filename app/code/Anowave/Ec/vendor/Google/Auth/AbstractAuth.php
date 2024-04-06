<?php
namespace Anowave\Ec\vendor\Google\Auth;

use Anowave\Ec\vendor\Google\Http\Request as Google_Http_Request;

abstract class AbstractAuth
{
	abstract public function authenticatedRequest(Google_Http_Request $request);
	abstract public function sign(Google_Http_Request $request);
}