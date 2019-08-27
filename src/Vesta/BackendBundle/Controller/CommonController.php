<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/common")
 */
class CommonController extends Controller
{
    /**
    * @Route("/leftMenu", options={"expose"=true})
    * @Template
    */
    public function leftMenuAction($routeName, Request $request)
    {
        $patern = "/[a-z]+_[a-z]+_([a-z]+)/";

        $matches = array();

        preg_match($patern, $routeName, $matches);

        if(isset($matches[1]) && $matches[1])
            $current = $matches[1];
        else
            $current = "contact";

        return array('current'=>$current);

    }

}
