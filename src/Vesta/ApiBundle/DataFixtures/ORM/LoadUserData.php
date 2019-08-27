<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\User;
use Cocur\Slugify\Slugify;

class LoadUserData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$slugify = new Slugify();
			$users = array();
			$users[] = array('name'=>'Administrator', 'username'=>'admin','password'=>'$2a$12$o2L2RQarUAi1fD.p6CtOQekVH3LOrapkVqPqVJcc82mheLLxHn7e.','email'=>'admin@example.com','is_active'=>1);

			foreach($users as $user)
			{
				$newUser = new User();
				$newUser->setName($user['name']);
				$newUser->setUsername($user['username']);
				$newUser->setPassword($user['password']);
				$newUser->setEmail($user['email']);
				$newUser->setIsActive($user['is_active']);

	            $errors = $validator->validate($newUser);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($newUser);
				$manager->flush();

				$this->addReference($slugify->slugify($user['username']), $newUser);
				unset($newUser);
			}
		}
		catch(\Exception $ex)
		{
			$logger = $this->container->get("logger");
			$logger->error(__CLASS__.": ".$ex->getMessage());

			switch ($ex->getCode())
			{
				case 100:
                    foreach ($errors as $error)
                    {
                        $property=$error->getPropertyPath();
                        $logger->error($property.": ".$error->getMessage());
                    }
					break;
				default:
					break;
			}

			throw $ex;
		}
	}

	public function getOrder()
	{
		return 1;
	}
}