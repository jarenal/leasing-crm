<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Organisation;

class LoadOrganisationData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');
			$faker->seed(500);
			$companies = array();
			$companies[] = array(
				'name'       => $faker->company,
				'phone'      => $faker->phoneNumber,
				'email'      => $faker->email,
				'website'    => $faker->url,
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				'organisation_type' => "local-authority",
				'comments'	=> $faker->text(300),
			);
			$companies[] = array(
				'name'       => $faker->company,
				'phone'      => $faker->phoneNumber,
				'email'      => $faker->email,
				'website'    => $faker->url,
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				'organisation_type' => "housing-association",
				'comments'	=> $faker->text(250),
			);
			$companies[] = array(
				'name'       => $faker->company,
				'phone'      => $faker->phoneNumber,
				'email'      => $faker->email,
				'website'    => $faker->url,
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				'organisation_type' => "support-agency",
				'comments'	=> "",
			);

			foreach($companies as $key => $company)
			{
				$new = new Organisation();
				$new->setName($company['name']);
				$new->setPhone($company['phone']);
				$new->setEmail($company['email']);
				$new->setWebsite($company['website']);
				$new->setAddress($company['address']);
				$new->setPostcode($company['postcode']);
				$new->setTown($company['town']);
				$new->setOrganisationType($this->getReference($company['organisation_type']));
				$new->setComments($company['comments']);
				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("company-$key", $new);

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
		return 4;
	}
}