<?php

namespace App\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

class UserController extends FOSRestController implements ClassResourceInterface
{
    public function cgetComboboxAction(Request $request)
    {
        try {
            $response = array();

            $term = $request->query->get('term');

            $entityManager = $this->getDoctrine()->getManager();
            $query = $entityManager->createQuery("SELECT c FROM AppApiBundle:Contact c INNER JOIN c.user u WHERE (c.name LIKE :name OR c.surname LIKE :name) AND c.deleted=0");
            $query->setParameter('name', "%$term%");
            $contacts = $query->getResult();

            foreach ($contacts as $contact) {

                $response[] = array('id'      => $contact->getId(),
                                    'value'   => $contact->getFullnameWithType(),
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

    public function postKeepaliveAction(Request $request)
    {
        $response = array('code'     => 0,
                          'data'     => array(),
                          'message'  => "success",
                          'errors'   => null);

        $session = $request->getSession();

        $current_time = time();

        if(!$session->get('start_time', 0))
        {
            $session->set('start_time', $current_time);
        }

        // max_idle: Maximum seconds idle in the current screen
        $max_idle = 10800; // 10800 senconds (3 hours)
        $response['data']['last_keepalive'] = $current_time;
        $response['data']['start_time'] = $session->get('start_time', 0);
        $idle_time = $response['data']['last_keepalive'] - $response['data']['start_time'];
        $response['data']['idle_time'] = $idle_time;

        if($idle_time > $max_idle)
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                $session->set('start_time', 0);
                $this->get('security.context')->setToken(null);
            }
        }

        $view = new View($response);
        $view->setFormat("json");
        return $this->handleView($view);
    }


    public function putChangePasswordAction($iduser, Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $response = array('code'     => 0,
                              'data'     => array(),
                              'message'  => "success",
                              'errors'   => null);

            $post = $request->request->get('user', array());

            // iduser required
            if (!$iduser) {
                throw new HttpException(100, "The iduser is required");
            }

            // exist user?
            $user = $em->find("\App\ApiBundle\Entity\User", $iduser);
            if(!$user)
                throw new HttpException(100, "The user with id \"{$iduser}\" doesn't exist.");


            $encoder = $this->container->get('security.password_encoder');

            // current password is ok?
            if(!$encoder->isPasswordValid($user, $post['current_password']))
                throw new HttpException(100, "The current password is not valid.");

            // new password is equal to current?
            if($post['current_password']==$post['new_password'])
                throw new HttpException(100, "You can't use the old password again.");

            $new_password_encoded = $encoder->encodePassword($user, $post['new_password']);

            $user->setPassword($new_password_encoded);
            $user->setPasswordUpdatedAt(new \DateTime());
            $em->persist($user);
            $em->flush();

        } catch (HttpException $ex) {
            $response['code']   = $ex->getStatusCode();
            $response['message'] = $ex->getMessage();
        }

        $view = new View($response);
        $view->setFormat("json");
        return $this->handleView($view);
    }
}
