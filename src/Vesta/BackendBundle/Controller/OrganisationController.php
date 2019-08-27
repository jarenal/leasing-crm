<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/organisation")
 */
class OrganisationController extends Controller
{
    /**
    * @Route("/list", options={"expose"=true})
    * @Template
    */
    public function indexAction()
    {
        return array();
    }

    /**
    * @Route("/new", options={"expose"=true})
    * @Template
    */
    public function newAction()
    {
        return $this->getStaticData();
    }

    /**
    * @Route("/show/{id}", options={"expose"=true})
    * @Template
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $id);

        /*
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($organisation, "json", $context);
        return array_merge($this->getStaticData(), array("organisation" => $json));*/
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("organisation" => $organisation, "context"=>$context));
    }

    /**
    * @Route("/edit/{id}", options={"expose"=true})
    * @Template
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // get contact from id
        $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $id);
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($organisation, "json", $context);
        return array_merge($this->getStaticData(), array("organisation" => $json));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        // Get Organisation types
        $query = $em->createQuery("SELECT ot FROM AppApiBundle:OrganisationType ot")
                ->setResultCacheDriver($predis)
                ->setResultCacheLifetime($max_lifetime);
        $organisation_types_result = $query->getResult();

        $organisation_types = array();

        foreach ($organisation_types_result as $type) {
            $organisation_types[] = array("id"=>$type->getId(), "name"=>$type->getName());
        }

        $static_data = array("property" => array(),
                     "organisation_types"       => $organisation_types,
                     );

        $json_static_data = json_encode($static_data);
        $redis->set("organisation_static_data", $json_static_data);

        return $static_data;
    }
}
