<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/ticket")
 */
class TicketController extends Controller
{
    /**
     * @Route("/list", options={"expose"=true})
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/new", options={"expose"=true})
     * @Template()
     */
    public function newAction()
    {
        return $this->getStaticData();
    }

    /**
     * @Route("/show/{id}", options={"expose"=true})
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->find("\App\ApiBundle\Entity\Ticket", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("ticket" => $task, "context"=>$context));
    }

    /**
     * @Route("/edit/{id}", options={"expose"=true})
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // get contact from id
        $ticket = $em->find("\App\ApiBundle\Entity\Ticket", $id);
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($ticket, "json", $context);
        return array_merge($this->getStaticData(), array("ticket" => $json));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        // Get ticket status
        $query = $em->createQuery("SELECT ts FROM AppApiBundle:TicketStatus ts")
                ->setResultCacheDriver($predis)
                ->setResultCacheLifetime($max_lifetime);
        $statuses = $query->getResult();

        $ticket_statuses = array();

        foreach ($statuses as $status) {
            $ticket_statuses[] = array("id"=>$status->getId(), "name"=>$status->getName());
        }

        // Get ticket types
        $query = $em->createQuery("SELECT tt FROM AppApiBundle:TicketType tt")
                ->setResultCacheDriver($predis)
                ->setResultCacheLifetime($max_lifetime);
        $types = $query->getResult();

        $ticket_types = array();

        foreach ($types as $type) {
            $ticket_types[] = array("id"=>$type->getId(), "name"=>$type->getName());
        }

        $ticket_static_data = array("ticket" => array(),
                     "ticket_statuses"   => $ticket_statuses,
                     "ticket_types"   => $ticket_types,
                     );

        return $ticket_static_data;
    }

}
