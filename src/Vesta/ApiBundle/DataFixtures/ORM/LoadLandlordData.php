<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Landlord;
use App\ApiBundle\Entity\TypeHasStatus;

class LoadLandlordData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');

			$users = array();

			$faker->seed(210);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-landlord-investor-unapproved",
							 'contact_title' => "Mr",
							 'contact_organisation' => "company-0",
							 'landlord_accreditation' => "landlord-accreditation-0",
							 'accreditation_references' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sed blandit justo. In tempor sollicitudin enim quis commodo. Nam suscipit, tellus nec dignissim fermentum, odio elit placerat est, id ornare dolor augue vitae sem. Pellentesque sit amet velit eu sem pellentesque cursus. Nam vel rhoncus libero, ultricies consequat tortor. Curabitur ac enim felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed turpis nulla, lobortis et feugiat sodales, fringilla ac lectus. Aenean dignissim quam ut nibh tincidunt, sed rhoncus metus finibus. Aliquam erat volutpat.',
							 'contact_method' => "method-email",
							 'contact_method_other' => "",
							 'comments' => $faker->text(500),
							 );

			$faker->seed(211);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-landlord-investor-pending-approval",
							 'contact_title' => "Miss",
							 'contact_organisation' => "company-1",
							 'landlord_accreditation' => "landlord-accreditation-1",
							 'accreditation_references' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sed blandit justo. In tempor sollicitudin enim quis commodo. Nam suscipit, tellus nec dignissim fermentum, odio elit placerat est, id ornare dolor augue vitae sem. Pellentesque sit amet velit eu sem pellentesque cursus. Nam vel rhoncus libero, ultricies consequat tortor. Curabitur ac enim felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed turpis nulla, lobortis et feugiat sodales, fringilla ac lectus. Aenean dignissim quam ut nibh tincidunt, sed rhoncus metus finibus. Aliquam erat volutpat.',
							 'contact_method' => "method-other",
							 'contact_method_other' => "Smoke signals",
							 'comments' => $faker->text(100),
							 );

			$faker->seed(212);
			$users[] = array('name'     => $faker->firstName,
							 'surname'  => $faker->lastName,
							 'email'    => $faker->email,
							 'landline' => $faker->phoneNumber,
							 'mobile'   => $faker->phoneNumber,
							 'address'  => $faker->streetAddress,
							 'postcode' => $faker->postcode,
							 'town'     => $faker->city,
							 'type_has_status' => "type-landlord-investor-approved",
							 'contact_title' => "Mrs",
							 'contact_organisation' => "company-2",
							 'landlord_accreditation' => "landlord-accreditation-2",
							 'accreditation_references' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sed blandit justo. In tempor sollicitudin enim quis commodo. Nam suscipit, tellus nec dignissim fermentum, odio elit placerat est, id ornare dolor augue vitae sem. Pellentesque sit amet velit eu sem pellentesque cursus. Nam vel rhoncus libero, ultricies consequat tortor. Curabitur ac enim felis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed turpis nulla, lobortis et feugiat sodales, fringilla ac lectus. Aenean dignissim quam ut nibh tincidunt, sed rhoncus metus finibus. Aliquam erat volutpat.',
							 'contact_method' => "",
							 'contact_method_other' => "",
							 'comments' => "",
							 );

			foreach($users as $key => $user)
			{
				$new = new Landlord();
				$new->setName($user['name']);
				$new->setSurname($user['surname']);
				$new->setEmail($user['email']);
				$new->setLandline($user['landline']);
				$new->setMobile($user['mobile']);
				$new->setAddress($user['address']);
				$new->setPostcode($user['postcode']);
				$new->setTown($user['town']);
				$new->setAccreditationReferences($user['accreditation_references']);

				$new->setTypeHasStatus($this->getReference($user['type_has_status']));

				$new->setContactTitle($this->getReference($user['contact_title']));
				$new->setOrganisation($this->getReference($user['contact_organisation']));
				$new->setLandlordAccreditation($this->getReference($user['landlord_accreditation']));

				if($user['contact_method'])
					$new->setContactMethod($this->getReference($user['contact_method']));
				$new->setContactMethodOther($user['contact_method_other']);
				$new->setComments($user['comments']);

				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("landlord-$key", $new);

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
		return 60;
	}
}