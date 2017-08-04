<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller{
	/**
	 * @Route("/login", name="login")
	 */
	public function loginAction(Request $request, AuthenticationUtils $authUtils){
		// Get login error
		$error = $authUtils->getLastAuthenticationError();
		// Get last username entered by the user
		$lastUserName = $authUtils->getLastUsername();
		return $this->render('security/login.html.twig', [
			'last_username' => $lastUserName,
			'error' => $error
		]);
	}
}