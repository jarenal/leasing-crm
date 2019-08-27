<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\ApiBundle\Entity\Organisation;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class OrganisationController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        try {
            $response = array('error'    => 0,
                              'data'     => array(),
                              'message'  => "success");

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT o.id, o.name, o.phone, o.email, o.address, o.postcode, o.town, ot.name organisation_type FROM AppApiBundle:Organisation o LEFT JOIN o.organisation_type ot WHERE o.deleted=0");
            $organisations = $query->getResult();
            $response['data'] = $organisations;
        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT org FROM AppApiBundle:Organisation org WHERE org.name LIKE :name AND org.deleted=0");
            $query->setParameter('name', "%$term%");
            $organisations = $query->getResult();

            foreach ($organisations as $organisation) {
                $response[] = array('id'      => $organisation->getId(),
                                    'value'   => $organisation->getFullnameWithType(),
                                    'address' => $organisation->getAddress(),
                                    'postcode' => $organisation->getPostcode(),
                                    'town' => $organisation->getTown(),
                                    );
            }

            $response[] = array("id"=>"", "value"=>"N/A");

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function cgetLocalAuthorityComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT org FROM AppApiBundle:Organisation org INNER JOIN org.organisation_type ot WHERE org.name LIKE :name AND org.deleted=0 AND ot.id=1");
            $query->setParameter('name', "%$term%");
            $organisations = $query->getResult();

            foreach ($organisations as $organisation) {
                $response[] = array('id'      => $organisation->getId(),
                                    'value'   => $organisation->getFullnameWithType(),
                                    'address' => $organisation->getAddress(),
                                    'postcode' => $organisation->getPostcode(),
                                    'town' => $organisation->getTown(),
                                    );
            }

            $response[] = array("id"=>"", "value"=>"N/A");

        } catch (HttpException $ex) {
            $response['error']   = $ex->getCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('organisation', array());

            $newOrganisation = $this->proccessData($post, $response);

            if ($newOrganisation) {
                $response['data'] = $newOrganisation;
            }

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->setGroups(array('onlyid'))
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function putAction($idorganisation, Request $request)
    {
        try {
            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('organisation', array());

            if (!$idorganisation) {
                throw new HttpException(100, "The idorganisation is required");
            }

            $this->proccessData($post, $response, $idorganisation);
        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    public function deleteAction($idorganisation, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            if ($idorganisation=="%s") {
                throw new HttpException(100, "The idorganisation is required");
            }

            $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $idorganisation);

            if (!$organisation) {
                throw new HttpException(100, "The organisation \"$idorganisation\" doesn't exist.");
            }

            $organisation->setDeleted(true);
            $em->persist($organisation);
            $em->flush();
        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        $context = SerializationContext::create()
                        ->enableMaxDepthChecks();
        $view->setSerializationContext($context);
        return $this->handleView($view);
    }

    private function proccessData($post, &$response, $idorganisation = null)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $validator = $this->get('validator');

            // are there any organisation using this email address?
            if(isset($post['email']) && $post['email'])
            {
                $duplicate = $em->getRepository("\App\ApiBundle\Entity\Organisation")->findOneBy(array("email"=>$post['email']));
            }

            if ($idorganisation)
            {
                $organisation = $em->find("\App\ApiBundle\Entity\Organisation", $idorganisation);
                if (!$organisation)
                {
                    throw new HttpException(100, "The Organisation with id \"$idorganisation\" doesn't exist.");
                }

                if(isset($duplicate) && $duplicate)
                {
                    if($organisation->getId() != $duplicate->getId())
                        throw new HttpException(100, "The email address '{$post['email']}' is already registered.");
                }

            }
            else
            {
                $organisation = new Organisation();

                if(isset($duplicate) && $duplicate)
                    throw new HttpException(100, "The email address '{$post['email']}' is already registered.");
            }

            if (isset($post['name'])) {
                $organisation->setName($post['name']);
            }
            if (isset($post['phone'])) {
                $organisation->setPhone($post['phone']);
            }
            if (isset($post['email'])) {
                $organisation->setEmail($post['email']);
            }
            if (isset($post['website'])) {
                $organisation->setWebsite($post['website']);
            }
            if (isset($post['address'])) {
                $organisation->setAddress($post['address']);
            }
            if (isset($post['postcode'])) {
                $organisation->setPostcode($post['postcode']);
            }
            if (isset($post['town'])) {
                $organisation->setTown($post['town']);
            }
            if (isset($post['comments'])) {
                $organisation->setComments($post['comments']);
            }

            // organisation type
            if (isset($post['organisation_type']) && $post['organisation_type']) {
                $organisationType = $em->find("\App\ApiBundle\Entity\OrganisationType", $post['organisation_type']);
                if ($organisationType) {
                    $organisation->setOrganisationType($organisationType);
                }
            } else {
                $organisation->setOrganisationType(null);
            }

            /* check validation */
            $errors = $validator->validate($organisation);

            if ($errors->count()) {
                foreach ($errors as $error) {
                    $response['errors'][] = array($error->getPropertyPath() => $error->getMessage());
                }
                throw new HttpException(100, "validation");
            }

            // save
            $em->persist($organisation);
            $em->flush();

            return array("id"=>$organisation->getId(), "name"=>$organisation->getName());
        } catch (HttpException $ex) {
            throw $ex;
        }
    }
}
