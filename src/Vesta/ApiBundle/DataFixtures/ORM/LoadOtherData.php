<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Other;
use App\ApiBundle\Utils\FakeNinGenerator;

class LoadOtherData  extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');

			$users = array();

			$faker->seed(330);
			$users[] = array("contact_title" => "Mr",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								'type_has_status' => "type-other-n-a",
								'contact_organisation' => "company-0",
								'job_title' => 'Manager',
								'comments' => $faker->text(500),
								'contact_method' => "method-email",
								'contact_method_other' => "",
								'other_type' => "other-type-0",
			);

			$faker->seed(331);
			$users[] = array("contact_title" => "Miss",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								'type_has_status' => "type-other-n-a",
								'contact_organisation' => "company-1",
								'job_title' => '',
								'comments' => '',
								'contact_method' => "method-other",
								'contact_method_other' => "Smoke signals",
								'other_type' => "other-type-4",
			);

			$faker->seed(332);
			$users[] = array("contact_title" => "Mrs",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								'type_has_status' => "type-other-n-a",
								'job_title' => 'Supervisor',
								'comments' => $faker->text(1000),
								'contact_method' => "",
								'contact_method_other' => "",
								'other_type' => "other-type-5",
			);

			foreach($users as $key => $user)
			{
				$new = new Other();
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
				$new->setJobTitle($user['job_title']);
				$new->setComments($user['comments']);
				if($user['contact_method'])
					$new->setContactMethod($this->getReference($user['contact_method']));
				$new->setContactMethodOther($user['contact_method_other']);

				if(isset($user['contact_organisation']))
					$new->setOrganisation($this->getReference($user['contact_organisation']));

				if($user['other_type'])
					$new->setOtherType($this->getReference($user['other_type']));

				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("other-".$key, $new);
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
		return 100;
	}
}