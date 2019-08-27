<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Contractor;

class LoadContractorData  extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$faker = \Faker\Factory::create('en_GB');
			$validator = $this->container->get("validator");

			$users = array();

			$faker->seed(230);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-contractor-unapproved",
							 'contact_title' => "Mr",
							 'contact_organisation' => "company-0",
							 'services' => array(0,1,2,3),
							 'areas' => array(0,1,2,3),
							 'contact_method' => "method-email",
							 'contact_method_other' => "",
							 'comments' => $faker->text(500),
							 );

			$faker->seed(231);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-contractor-pending-approval",
							 'contact_title' => "Miss",
							 'contact_organisation' => "company-1",
							 'services' => array(4,5,6,7),
							 'areas' => array(4,5,6,7),
							 'contact_method' => "method-other",
							 'contact_method_other' => "Smoke signals",
							 'comments' => $faker->text(100),
							 );

			$faker->seed(232);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-contractor-approved",
							 'contact_title' => "Mrs",
							 'contact_organisation' => "company-2",
							 'services' => array(8,9,10,11),
							 'areas' => array(8,9,10,11,12),
							 'contact_method' => "",
							 'contact_method_other' => "",
							 'comments' => "",
							 );

			foreach($users as $user)
			{
				$new = new Contractor();
				$new->setName($user['name']);
				$new->setSurname($user['surname']);
				$new->setEmail($user['email']);
				$new->setLandline($user['landline']);
				$new->setMobile($user['mobile']);
				$new->setAddress($user['address']);
				$new->setPostcode($user['postcode']);
				$new->setTown($user['town']);
				$new->setTypeHasStatus($this->getReference($user['type_has_status']));
				$new->setContactTitle($this->getReference($user['contact_title']));
				$new->setOrganisation($this->getReference($user['contact_organisation']));
				$new->setRequireCertification(rand(0,1)?true:false);
				$new->setComments($user['comments']);

				if(is_array($user['services']) && $user['services'])
				{
					foreach ($user['services'] as $service)
					{
						$new->addService($this->getReference("service-".$service));
					}
				}
				if(is_array($user['areas']) && $user['areas'])
				{
					foreach ($user['areas'] as $area)
					{
						$new->addArea($this->getReference("area-".$area));
					}
				}

				if($user['contact_method'])
					$new->setContactMethod($this->getReference($user['contact_method']));
				$new->setContactMethodOther($user['contact_method_other']);

				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();
				unset($new);
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
		return 80;
	}
}