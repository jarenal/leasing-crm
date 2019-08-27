<?php

namespace App\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApiWebTestCase extends WebTestCase
{
	protected $client = null;

	protected function setUp()
	{
		// debug: Leave false for reduce error output.
		$this->client = static::createClient(array('environment' => 'test',
    											   'debug'       => false), array(
														'HTTP_CONTENT_TYPE'          => 'application/json',
														'HTTP_X_REQUESTED_WITH'      => 'XMLHttpRequest',
														'HTTP_ACCEPT'                => 'application/json'
    					));

		$container = $this->client->getContainer();
		$doctrine = $container->get('doctrine');
		$em = $doctrine->getEntityManager();

        $session = $this->client->getContainer()->get('session');
        $firewall = 'default';

		$user = $em->find("\App\ApiBundle\Entity\User", 1);

        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
	}
}