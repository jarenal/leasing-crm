<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
	Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
	/**
	* @Route("/login", name="login_route")
	* @Template()
	*/
	public function loginAction()
	{
		$authenticationUtils = $this->get("security.authentication_utils");

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return array(
				// last username entered by the user
				'last_username' => $lastUsername,
				'error' => $error,
		);
	}

	/**
	* @Route("/login_check", name="login_check")
	*/
	public function loginCheckAction()
	{
		// This controller will not be executed,
		// as the route is handled by the security system
	}
}