<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Doctrine\Common\Cache\PredisCache;

/**
 * @Route("/breakdown")
 */
class BreakdownController extends Controller
{
    /**
     * @Route("/list/{tenancy}", options={"expose"=true})
     * @Template()
     */
    public function indexAction($tenancy)
    {
        $static_data = array();

        if($tenancy)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->find("\App\ApiBundle\Entity\Tenancy", $tenancy);
            $static_data['tenancy'] = array("id"=>$entity->getId());
        }
        else
        {
            $static_data = array("tenancy" => array());
        }

        return $static_data;
    }

    /**
     * @Route("/new/{tenancy}", options={"expose"=true})
     * @Template()
     */
    public function newAction($tenancy="")
    {
        $breakdown = array();

        if($tenancy)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->find("\App\ApiBundle\Entity\Tenancy", $tenancy);

            if($entity)
                $breakdown = array("tenancy"=>array("id"=>$entity->getId()));
        }

        return array_merge($this->getStaticData(), array("breakdown" => $breakdown));
    }

    /**
     * @Route("/edit/{id}", options={"expose"=true})
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $breakdown = $em->find("\App\ApiBundle\Entity\RentBreakdown", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("breakdown" => $breakdown, "context"=>$context));
    }

    /**
     * @Route("/show/{id}", options={"expose"=true})
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $breakdown = $em->find("\App\ApiBundle\Entity\RentBreakdown", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("breakdown" => $breakdown, "context"=>$context));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery("SELECT rbi FROM AppApiBundle:RentBreakdownItem rbi WHERE rbi.isActive=true ORDER BY rbi.id ASC")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $items = $query->getResult();

        $static_data = array("items" => $items);

        return $static_data;
    }
}