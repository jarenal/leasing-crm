<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/property")
 */
class PropertyController extends Controller
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

        $property = $em->find("\App\ApiBundle\Entity\Property", $id);

        //$fb = $this->get('my_fire_php');
        /*
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($property, "json", $context);
        return array_merge($this->getStaticData(), array("property" => $json));
        */
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("property" => $property, "context"=>$context));
    }

    /**
    * @Route("/edit/{id}", options={"expose"=true})
    * @Template
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // get contact from id

        $property = $em->find("\App\ApiBundle\Entity\Property", $id);
        /*
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($property, "json", $context);*/
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("property" => $property, "context"=>$context));
    }

    /**
    * @Route("/risks-assessments/{id}", options={"expose"=true})
    * @Template
    */
    public function risksAssessmentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $property = $em->find("\App\ApiBundle\Entity\Property", $id);
        $query = $em->createQuery("SELECT c FROM AppApiBundle:RiskAssessmentCategory c WHERE c.isActive=true AND c.type=1 ORDER BY c.name ASC");
        $categories = $query->getResult();
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("property" => $property, "categories"=> $categories, "context"=>$context));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        // Get Property status
        $query = $em->createQuery("SELECT ps FROM AppApiBundle:PropertyStatus ps")
                ->setResultCacheDriver($predis)
                ->setResultCacheLifetime($max_lifetime);
        $statuses = $query->getResult();

        $property_statuses = array();

        foreach ($statuses as $status) {
            $property_statuses[] = array("id"=>$status->getId(), "name"=>$status->getName());
        }

        // Get Organisation types
        $query = $em->createQuery("SELECT ot FROM AppApiBundle:OrganisationType ot")
                ->setResultCacheDriver($predis)
                ->setResultCacheLifetime($max_lifetime);
        $organisation_types_result = $query->getResult();

        $organisation_types = array();

        foreach ($organisation_types_result as $type) {
            $organisation_types[] = array("id"=>$type->getId(), "name"=>$type->getName());
        }

        $levels_of_risk = array();
        $levels_of_risk[] = array("id"=>"Low", "name"=>"Low");
        $levels_of_risk[] = array("id"=>"Medium", "name"=>"Medium");
        $levels_of_risk[] = array("id"=>"High", "name"=>"High");
        $levels_of_risk[] = array("id"=>"N/A", "name"=>"N/A");

        $property_static_data = array("property" => array(),
                     "property_statuses"   => $property_statuses,
                     "organisation_types"  => $organisation_types,
                     "levels_of_risk"      => $levels_of_risk,
                     );

        return $property_static_data;
    }
}
