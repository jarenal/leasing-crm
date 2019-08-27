<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
    * @Route("/saludo/{name}")
    */	
    public function indexAction($name)
    {
        return $this->render('AppApiBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
    * @Route("/adminjose")
    */
    public function adminAction()
    {
        $data = array();
        $data['name'] = "Pepe Luis";
        $data['surname'] = "Rodríguez";
        $data['colors'] = array('red','green','blue');

        $serializer = $this->get('jms_serializer');
        $result = $serializer->serialize($data, 'json');

    	return new Response("Admin page Jose! $result");        
    }
}
