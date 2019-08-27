<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/lease-agreement")
 */
class LeaseAgreementController extends Controller
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
        $lease_agreement = array();

        if($property)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->find("\App\ApiBundle\Entity\Property", $property);

            if($entity)
                $lease_agreement = array("property"=>array("id"=>$entity->getId(), "fulltitle"=>$entity->getFulltitle()));
        }

        return array_merge($this->getStaticData(), array("lease_agreement" => $lease_agreement));
    }

    /**
     * @Route("/show/{id}", options={"expose"=true})
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $lease_agreement = $em->find("\App\ApiBundle\Entity\LeaseAgreement", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("lease_agreement" => $lease_agreement, "context"=>$context));
    }

    /**
     * @Route("/edit/{id}", options={"expose"=true})
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // get contact from id
        $lease_agreement = $em->find("\App\ApiBundle\Entity\LeaseAgreement", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("lease_agreement" => $lease_agreement, "context"=>$context));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        $static_data = array("lease_agreement" => array(),
                     );

        return $static_data;
    }

}
