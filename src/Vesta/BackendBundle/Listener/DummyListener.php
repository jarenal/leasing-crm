<?php
namespace App\BackendBundle\Listener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class DummyListener
{
	private $tokenStorage;
	private $authorizationChecker;
	private $logger;
	private $router;

	/**
	 * Constructor
	 *
	 * @param TokenStorage $tokenStorage
	 * @param Doctrine        $doctrine
	 */
	public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, $logger, $router)
	{
		$this->authorizationChecker = $authorizationChecker;
		$this->tokenStorage = $tokenStorage;
		$this->logger		   = $logger;
		$this->router = $router;
	}

	public function onKernelRequestEvent(GetResponseEvent $event) {

		/* TODO Example of kernel.request event */
		//$this->logger->info('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ onMustChangepasswordEvent');
	}

	public function onKernelResponseEvent(FilterResponseEvent $event)
	{
		/* TODO Example for to modify a response using kernel.response event
	    $response1 = $event->getResponse();
	    $this->logger->info('************************************************************************************ onKernelResponse');
	    // ... modify the response object

		$response2 = new Response(
		    $response1->getContent(),
		    Response::HTTP_OK,
		    array('content-type' => 'text/html')
		);

		//$response->setContent("Hello World!");
	    $event->setResponse($response2);*/
	}
}