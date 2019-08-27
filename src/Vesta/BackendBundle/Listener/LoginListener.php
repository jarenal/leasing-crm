<?php
namespace App\BackendBundle\Listener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+

/**
 * Custom login listener.
 */
class LoginListener
{
	private $authorizationChecker;
	private $em;

	/**
	 * Constructor
	 *
	 * @param AuthorizationChecker $authorizationChecker
	 * @param Doctrine        $doctrine
	 */
	public function __construct(AuthorizationChecker $authorizationChecker, Doctrine $doctrine)
	{
		$this->authorizationChecker = $authorizationChecker;
		$this->em              = $doctrine->getEntityManager();
	}

	/**
	 * Do the magic.
	 *
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
			// user has just logged in
			$user = $event->getAuthenticationToken()->getUser();
	        $user->setLastLoginAt(new \DateTime());

	        $this->em->persist($user);
	        $this->em->flush();
		}

		if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			// user has logged in using remember_me cookie
		}

		// do some other magic here
	}
}