<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/tenancy")
 */
class TenancyController extends Controller
{
    /**
     * @Route("/list/{property}", options={"expose"=true})
     * @Template()
     */
    public function indexAction($property="")
    {
        $static_data = array();

        if($property)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->find("\App\ApiBundle\Entity\Property", $property);
            $static_data['property'] = array("id"=>$entity->getId(), "address"=>$entity->getAddress());
        }
        else
        {
            $static_data = array("property" => array());
        }

        return $static_data;
    }

    /**
     * @Route("/new/{property}", options={"expose"=true})
     * @Template()
     */
    public function newAction($property="")
    {
        $tenancy = array();

        if($property)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->find("\App\ApiBundle\Entity\Property", $property);

            if($entity)
                $tenancy = array("property"=>array("id"=>$entity->getId(), "fulltitle"=>$entity->getFulltitle()));
        }

        return array_merge($this->getStaticData(), array("tenancy" => $tenancy));
    }

    /**
     * @Route("/edit/{id}", options={"expose"=true})
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $tenancy = $em->find("\App\ApiBundle\Entity\Tenancy", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("tenancy" => $tenancy, "context"=>$context));
    }

    /**
     * @Route("/show/{id}", options={"expose"=true})
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $tenancy = $em->find("\App\ApiBundle\Entity\Tenancy", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("tenancy" => $tenancy, "context"=>$context));
    }

    private function getStaticData()
    {
        /*
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        $static_data = array("tenancy" => array());

        return $static_data;
        */
        return array();
    }
}