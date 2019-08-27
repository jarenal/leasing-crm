<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Property;

class LoadPropertyData  extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');

			$properties = array();

			$faker->seed(320);
			$properties[] = array(
				"landlord" => "landlord-0",
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				"local_authority" => "company-0",
				"available_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"parking_for" => "",
				"special_design_features" => $faker->text(500),
				"previous_crimes_description" => $faker->text(200),
				"property_value" => $faker->randomNumber(6),
				"valuation_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"target_rent" => $faker->randomNumber(3),
				"mortgage_outstanding" => true,
				"buy_to_let" => false,
				"status" => "property-status-0",
				"features" => array("detached","1-bedroom", "furnished", "no-parking", "garden-required","front-garden","fully-wheelchair-accessible","smoking-allowed","pets-are-allowed","willing-fully-wheelchair-accessible"),
			);

			$faker->seed(321);
			$properties[] = array(
				"landlord" => "landlord-1",
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				"local_authority" => "company-0",
				"available_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"parking_for" => "1",
				"special_design_features" => $faker->text(300),
				"previous_crimes_description" => $faker->text(400),
				"property_value" => $faker->randomNumber(4),
				"valuation_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"target_rent" => $faker->randomNumber(2),
				"mortgage_outstanding" => true,
				"buy_to_let" => false,
				"status" => "property-status-1",
				"features" => array("terraced","2-bedrooms", "unfurnished", "with-parking", "garden-preferred","large-back-garden","minor-adaptations","smoking-not-allowed","pets-are-not-allowed","willing-none"),
			);

			$faker->seed(322);
			$properties[] = array(
				"landlord" => "landlord-2",
				'address'    => $faker->streetAddress,
				'postcode'	 => $faker->postcode,
				'town'	 	 => $faker->city,
				"local_authority" => "company-0",
				"available_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"parking_for" => "2",
				"special_design_features" => $faker->text(1000),
				"previous_crimes_description" => $faker->text(250),
				"property_value" => $faker->randomNumber(7),
				"valuation_date" => $faker->date($format = 'd/m/Y', $max="now"),
				"target_rent" => $faker->randomNumber(4),
				"mortgage_outstanding" => true,
				"buy_to_let" => false,
				"status" => "property-status-2",
				"features" => array("flats-apartments","4-bedrooms", "part-furnished", "with-parking", "no-garden-required","none","smoking-allowed","pets-are-allowed","willing-minor-adaptations"),
			);

			foreach($properties as $key => $property)
			{
				$new = new Property();

				$new->setAddress($property['address']);
				$new->setPostcode($property['postcode']);
				$new->setTown($property['town']);
				$new->setAvailableDate($property['available_date']);
				$new->setParkingFor($property['parking_for']);
				$new->setSpecialDesignFeatures($property['special_design_features']);
				$new->setPreviousCrimesDescription($property['previous_crimes_description']);
				$new->setPropertyValue($property['property_value']);
				$new->setValuationDate($property['valuation_date']);
				$new->setTargetRent($property['target_rent']);
				$new->setMortgageOutstanding($property['mortgage_outstanding']);
				$new->setBuyToLet($property['buy_to_let']);

				if(isset($property['landlord']) && $property['landlord'])
				{
					$new->setLandlord($this->getReference($property['landlord']));
				}

				if(isset($property['local_authority']) && $property['local_authority'])
				{
					$new->setLocalAuthority($this->getReference($property['local_authority']));
				}

				if(isset($property['status']) && $property['status'])
				{
					$new->setStatus($this->getReference($property['status']));
				}

				if(isset($property["features"]) && $property["features"])
				{
					foreach ($property["features"] as $feature) {
						$new->addFeature($this->getReference($feature));
					}
				}

				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("property-$key", $new);
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
		return 200;
	}
}