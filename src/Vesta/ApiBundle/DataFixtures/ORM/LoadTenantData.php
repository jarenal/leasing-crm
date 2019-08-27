<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Tenant;
use App\ApiBundle\Utils\FakeNinGenerator;

class LoadTenantData  extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');
			$fakeNinGenerator = new FakeNinGenerator();

			$users = array();

			$faker->seed(220);
			$users[] = array("contact_title" => "Mr",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								"birthdate" => $faker->date($format = 'd/m/Y', $max = '15 years ago'),
								"gender" => "male",
								"marital_status" => "single",
								"nin" => $fakeNinGenerator->generateNINO(),
								"local_authority" => "company-0",
								'type_has_status' => "type-tenant-needs-housing",
								"nights_support" => array(0),
								"agency_support_provider" => "company-1",
								"contact_support_provider" => "other-0",
								"lack_capacity" => 1,
								"deputy" => "other-1",
								"housingRegister" => 1,
								"moveDate" => $faker->date($format = 'd/m/Y', $max="now"),
								'areas' => array(0,1,2,3),
								"outCounty" => 1,
								"specialDesignFeatures" => $faker->text(300),
								"tenantPersonality" => $faker->text(500),
								"willingToShare" => 1,
								"drugHistorial" => 1,
								"drugHistorialDetails" => $faker->text(250),
								"sexualOffencesHistorial" => 1,
								"sexualOffencesHistorialDetails" => $faker->text(250),
								"arsonHistorial" => 1,
								"arsonHistorialDetails" => $faker->text(250),
								"evictionsHistorial" => 1,
								"evictionsHistorialDetails" => $faker->text(250),
								"tenantReferences" => $faker->text(1000),
								"requirements" => array("detached","1-bedroom", "furnished", "no-parking", "garden-required","front-garden","fully-wheelchair-accessible","smoking-allowed","pets-are-allowed"),
								"parkingFor" => 0,
								"social_services_contact" => "other-1",
								'contact_method' => "method-email",
								'contact_method_other' => "",
								'comments' => $faker->text(500),
								'lfl_contact' => "other-2",
			);

			$faker->seed(221);
			$users[] = array("contact_title" => "Miss",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								"birthdate" => $faker->date($format = 'd/m/Y', $max = '15 years ago'),
								"gender" => "female",
								"marital_status" => "married",
								"nin" => $fakeNinGenerator->generateNINO(),
								"local_authority" => "company-1",
								'type_has_status' => "type-tenant-pending-housing",
								"nights_support" => array(0,1),
								"agency_support_provider" => "company-2",
								"contact_support_provider" => "",
								"lack_capacity" => 0,
								"deputy" => "other-2",
								"housingRegister" => 0,
								"moveDate" => $faker->date($format = 'd/m/Y', $max="now"),
								'areas' => array(4,5,6,7),
								"outCounty" => 0,
								"specialDesignFeatures" => "",
								"tenantPersonality" => "",
								"willingToShare" => 0,
								"drugHistorial" => 0,
								"drugHistorialDetails" => "",
								"sexualOffencesHistorial" => 0,
								"sexualOffencesHistorialDetails" => "",
								"arsonHistorial" => 0,
								"arsonHistorialDetails" => "",
								"evictionsHistorial" => 0,
								"evictionsHistorialDetails" => "",
								"tenantReferences" => "",
								"requirements" => array("detached","semi-detached","2-bedrooms", "part-furnished", "with-parking", "garden-preferred","front-garden","back-garden","minor-adaptations","smoking-not-allowed","pets-are-not-allowed"),
								"parkingFor" => 2,
								"social_services_contact" => "",
								'contact_method' => "method-other",
								'contact_method_other' => "Smoke signals",
								'comments' => $faker->text(100),
								'lfl_contact' => "other-1",
			);

			$faker->seed(222);
			$users[] = array("contact_title" => "Mrs",
								'name'     => $faker->firstName,
								'surname'  => $faker->lastName,
								'email'    => $faker->email,
								'landline' => $faker->phoneNumber,
								'mobile'   => $faker->phoneNumber,
								'address'  => $faker->streetAddress,
								'postcode' => $faker->postcode,
								'town'     => $faker->city,
								"birthdate" => $faker->date($format = 'd/m/Y', $max = '15 years ago'),
								"gender" => "transgender",
								"marital_status" => "civil-partnership",
								"nin" => $fakeNinGenerator->generateNINO(),
								"local_authority" => "company-2",
								'type_has_status' => "type-tenant-housed",
								"nights_support" => array(),
								"agency_support_provider" => "",
								"contact_support_provider" => "other-1",
								"lack_capacity" => 1,
								"deputy" => "",
								"housingRegister" => 1,
								"moveDate" => $faker->date($format = 'd/m/Y', $max="now"),
								'areas' => array(8,9,10,11,12),
								"outCounty" => 0,
								"specialDesignFeatures" => $faker->text(300),
								"tenantPersonality" => $faker->text(500),
								"willingToShare" => 1,
								"drugHistorial" => 0,
								"drugHistorialDetails" => "",
								"sexualOffencesHistorial" => 1,
								"sexualOffencesHistorialDetails" => $faker->text(250),
								"arsonHistorial" => 0,
								"arsonHistorialDetails" => "",
								"evictionsHistorial" => 1,
								"evictionsHistorialDetails" => $faker->text(250),
								"tenantReferences" => $faker->text(1000),
								"requirements" => array("flats-apartments","5-bedrooms","4-bedrooms", "unfurnished", "with-parking", "no-garden-required","none","smoking-allowed","pets-are-not-allowed"),
								"parkingFor" => 4,
								"social_services_contact" => "other-2",
								'contact_method' => "",
								'contact_method_other' => "",
								'comments' => "",
								'lfl_contact' => "",
			);

			foreach($users as $key => $user)
			{
				$new = new Tenant();
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
				$new->setBirthdate($user['birthdate']);
				$new->setGender($this->getReference($user['gender']));
				$new->setMaritalStatus($this->getReference($user['marital_status']));
				$new->setNin($user['nin']);
				$new->setLocalAuthority($this->getReference($user['local_authority']));
				$new->setComments($user['comments']);

				if(is_array($user['nights_support']) && $user['nights_support'])
				{
					foreach ($user['nights_support'] as $night_support)
					{
						$new->addNightsSupport($this->getReference("tenant-nights-support-".$night_support));
					}
				}

				if($user['agency_support_provider'])
				{
					$new->setAgencySupportProvider($this->getReference($user['agency_support_provider']));
				}

				if($user['contact_support_provider'])
				{
					$new->setContactSupportProvider($this->getReference($user['contact_support_provider']));
				}

				$new->setLackCapacity($user['lack_capacity']);

				if($user['deputy'])
				{
					$new->setDeputy($this->getReference($user['deputy']));
				}
				$new->setHousingRegister($user['housingRegister']);
				$new->setMoveDate($user['moveDate']);
				if(is_array($user['areas']) && $user['areas'])
				{
					foreach ($user['areas'] as $area)
					{
						$new->addArea($this->getReference("tenant-area-".$area));
					}
				}
				$new->setOutCounty($user['outCounty']);
				$new->setSpecialDesignFeatures($user['specialDesignFeatures']);
				$new->setTenantPersonality($user['tenantPersonality']);
				$new->setWillingToShare($user['willingToShare']);
				$new->setDrugHistorial($user['drugHistorial']);
				$new->setDrugHistorialDetails($user['drugHistorialDetails']);
				$new->setSexualOffencesHistorial($user['sexualOffencesHistorial']);
				$new->setSexualOffencesHistorialDetails($user['sexualOffencesHistorialDetails']);
				$new->setArsonHistorial($user['arsonHistorial']);
				$new->setArsonHistorialDetails($user['arsonHistorialDetails']);
				$new->setEvictionsHistorial($user['evictionsHistorial']);
				$new->setEvictionsHistorialDetails($user['evictionsHistorialDetails']);
				$new->setTenantReferences($user['tenantReferences']);
				$new->setParkingFor($user['parkingFor']);

				if($user['social_services_contact'])
				{
					$new->setSocialServicesContact($this->getReference($user['social_services_contact']));
				}

				if(isset($user["requirements"]) && $user["requirements"])
				{
					foreach ($user["requirements"] as $requirement) {
						$new->addRequirement($this->getReference($requirement));
					}
				}

				if($user['contact_method'])
					$new->setContactMethod($this->getReference($user['contact_method']));

				if($user['lfl_contact'])
				{
					$new->setLflContact($this->getReference($user['lfl_contact']));
				}

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

				$this->addReference("tenant-$key", $new);

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
		return 205;
	}
}