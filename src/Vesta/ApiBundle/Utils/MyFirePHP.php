<?php

namespace App\ApiBundle\Utils;

class MyFirePHP {

	private $firephp;
	private $debug;

	public function __construct($firephp="", $debug){
		$this->firephp = $firephp;
		$this->debug = $debug;
	}

	public function log($var, $message){
		if($this->debug)
		{
			if($this->firephp)
			{
				$this->firephp->log($var, $message);
			}
		}
	}
}