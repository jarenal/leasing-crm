<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $authorizationChecker = $this->get('security.authorization_checker');
        $change_password = false;
        $user = array();

        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->getUser();
            $now = time();
            $password_updated_at = $user->getPasswordUpdatedAt()->getTimestamp();
            $difference = $now - $password_updated_at;

            if($difference > 7776000) // 7776000 is 3 months
                $change_password = true;

        }

        return array("change_password"=>$change_password, "user"=>$user);
    }

}
