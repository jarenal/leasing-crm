<?php

namespace App\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Cache\PredisCache;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/contact")
 */
class ContactController extends Controller
{
    /**
    * @Route("/", options={"expose"=true})
    * @Template
    */
    public function indexAction()
    {
        $user = $this->getUser();
        $fb = $this->get('my_fire_php');
        $fb->log($user->getName(), "user name session");
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

        $contact = $em->find("\App\ApiBundle\Entity\Contact", $id);
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("contact" => $contact, "context"=>$context));
    }

    /**
    * @Route("/edit/{id}", options={"expose"=true})
    * @Template
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // get contact from id
        $contact = $em->find("\App\ApiBundle\Entity\Contact", $id);

        return array_merge($this->getStaticData(), array("contact" => $contact));
    }

    /**
    * @Route("/risks-assessments/{id}", options={"expose"=true})
    * @Template
    */
    public function risksAssessmentsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->find("\App\ApiBundle\Entity\Contact", $id);

        $contact_type = $contact->getContactType()->getId();

        switch ($contact_type)
        {
            case 1:
                $category_type=3;
            break;
            case 2:
                $category_type=2;
            break;
        }

        $query = $em->createQuery("SELECT c FROM AppApiBundle:RiskAssessmentCategory c WHERE c.isActive=true AND c.type=:type ORDER BY c.name ASC");
        $query->setParameter("type", $category_type);
        $categories = $query->getResult();
        $context = SerializationContext::create()->enableMaxDepthChecks();
        return array_merge($this->getStaticData(), array("contact" => $contact, "categories"=> $categories, "context"=>$context));
    }

    private function getStaticData()
    {
        $fb = $this->get('my_fire_php');
        $redis = $this->container->get('snc_redis.default');
        $predis = new PredisCache($redis);
        $max_lifetime = $this->getParameter('app_backend.redis_max_lifetime');
        $fb->log($max_lifetime, "REDIS MAX LIFETIME...");

        $em = $this->getDoctrine()->getManager();

        // Get Landlord Statuses
        $query = $em->createQuery("SELECT ths  FROM AppApiBundle:TypeHasStatus ths INNER JOIN ths.status s INNER JOIN ths.type t ORDER BY ths.type ASC, ths.status ASC")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $typeHasStatus = $query->getResult();
        //\Doctrine\Common\Util\Debug::dump($typeHasStatus);
        $contact_statuses = array();

        foreach ($typeHasStatus as $item) {
            $contact_statuses[$item->getType()->getId()][] = array("id"=>$item->getStatus()->getId(),"name"=>$item->getStatus()->getName());
        }

        // Get Contact types
        $query = $em->createQuery("SELECT cst FROM AppApiBundle:ContactType cst")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $types = $query->getResult();

        $contact_types = array();

        foreach ($types as $type) {
            $contact_types[] = array("id"=>$type->getId(), "name"=>$type->getName());
        }

        // Get Landlord Accreditations
        $query = $em->createQuery("SELECT la FROM AppApiBundle:LandlordAccreditation la")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $accreditations = $query->getResult();

        $landlord_accreditations = array();

        foreach ($accreditations as $accreditation) {
            $landlord_accreditations[] = array("id"=>$accreditation->getId(), "name"=>$accreditation->getName());
        }

        // Get Contact titles
        $query = $em->createQuery("SELECT ct FROM AppApiBundle:ContactTitle ct")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $titles = $query->getResult();

        $contact_titles = array();

        foreach ($titles as $title) {
            $contact_titles[] = array("id"=>$title->getId(), "name"=>$title->getName());
        }

        // Get Contractor Services
        $query = $em->createQuery("SELECT s FROM AppApiBundle:Service s")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $services = $query->getResult();

        $contractor_services = array();

        foreach ($services as $service) {
            $contractor_services[] = array("id"=>$service->getId(), "name"=>$service->getName());
        }

        // Get Tenant Genders
        $query = $em->createQuery("SELECT g FROM AppApiBundle:Gender g")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $genders = $query->getResult();

        $tenant_genders = array();

        foreach ($genders as $gender) {
            $tenant_genders[] = array("id"=>$gender->getId(), "name"=>$gender->getName());
        }
        // Get Tenant Marital Statuses
        $query = $em->createQuery("SELECT ms FROM AppApiBundle:MaritalStatus ms")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $marital_statuses = $query->getResult();

        $tenant_marital_statuses = array();

        foreach ($marital_statuses as $marital_status) {
            $tenant_marital_statuses[] = array("id"=>$marital_status->getId(), "name"=>$marital_status->getName());
        }

        // Get Tenant Nights support
        $query = $em->createQuery("SELECT t FROM AppApiBundle:TenantNightsSupport t")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $nights_support = $query->getResult();

        $tenant_nights_support = array();

        foreach ($nights_support as $support) {
            $tenant_nights_support[] = array("id"=>$support->getId(), "name"=>$support->getName());
        }

        // Conditions
        $query = $em->createQuery("SELECT tc, c FROM AppApiBundle:TenantCondition tc LEFT JOIN tc.children c WHERE tc.parent IS NULL")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $conditions = $query->getResult();

        $tenant_conditions = array();

        foreach ($conditions as $condition) {
            $children = array();
            $data = array("id"=>$condition->getId(), "name"=>$condition->getName(), "is_other"=>$condition->getIsOther());

            if ($condition->getChildren()->count()) {
                foreach ($condition->getChildren() as $child) {
                    $children[] = array("id"=>$child->getId(), "name"=>$child->getName(), "is_other"=>$child->getIsOther());
                }
            }

            $tenant_conditions[] = array_merge($data, array('children'=>$children));
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

        // Get Other types
        $query = $em->createQuery("SELECT ot FROM AppApiBundle:OtherType ot")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $other_types_result = $query->getResult();

        $other_types = array();

        foreach ($other_types_result as $type) {
            $other_types[] = array("id"=>$type->getId(), "name"=>$type->getName());
        }

        // Get Contact methods
        $query = $em->createQuery("SELECT cm FROM AppApiBundle:ContactMethod cm")
                    ->setResultCacheDriver($predis)
                    ->setResultCacheLifetime($max_lifetime);
        $methods = $query->getResult();

        $contact_methods = array();

        foreach ($methods as $method) {
            $contact_methods[] = array("id"=>$method->getId(), "name"=>$method->getName());
        }

        $levels_of_risk = array();
        $levels_of_risk[] = array("id"=>"Low", "name"=>"Low");
        $levels_of_risk[] = array("id"=>"Medium", "name"=>"Medium");
        $levels_of_risk[] = array("id"=>"High", "name"=>"High");
        $levels_of_risk[] = array("id"=>"N/A", "name"=>"N/A");

        $static_data = array("contact" => array(),
                     "types"   => array(),
                     "contact_statuses" =>        $contact_statuses,
                     "landlord_accreditations" => $landlord_accreditations,
                     "contact_titles" =>          $contact_titles,
                     "contact_types" =>           $contact_types,
                     "contractor_services" =>     $contractor_services,
                     "tenant_genders" =>          $tenant_genders,
                     "tenant_marital_statuses" => $tenant_marital_statuses,
                     "tenant_nights_support" =>   $tenant_nights_support,
                     "tenant_conditions"       => $tenant_conditions,
                     "organisation_types"       => $organisation_types,
                     "other_types"       => $other_types,
                     "contact_methods" => $contact_methods,
                     "levels_of_risk" => $levels_of_risk,
                     );

        return $static_data;
    }
}
