<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Feature;
use Cocur\Slugify\Slugify;

class LoadFeatureData  extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$slugify = new Slugify();

			$requirements = array();

			// Property type
			$requirements["property-type"] = array("Detached", "Terraced", "Flats/Apartments", "Semi-detached", "Bungalow");

			// Bedrooms
			$requirements["bedrooms"] = array("1 bedroom", "2 bedrooms", "3 bedrooms", "4 bedrooms", "5 bedrooms", "6 bedrooms");

			// Furniture
			$requirements["furnitures"] = array("Furnished", "Unfurnished", "Part furnished");

			// Parking
			$requirements["parking"] = array("No parking", "With parking");

			// Garden
			$requirements["garden"] = array("Garden required", "Garden preferred", "No garden required");

			// Garden details
			$requirements["garden-details"] = array("Front garden", "Fenced and secure", "Large back garden", "Back garden", "Private/not overlooked", "Communal garden");

			// Accessible
			$requirements["accessibility"] = array("Fully wheelchair accessible", "Minor adaptations", "None");

			// Smoking
			$requirements["smoking"] = array("Smoking allowed", "Smoking not allowed");

			// Pets
			$requirements["pets"] = array("Pets are allowed", "Pets are not allowed");

			// Willing to adapt
			$requirements["willing-to-adapt"] = array("Fully wheelchair accessible", "Minor adaptations", "None");

			// Floors
			$requirements["floors"] = array("1 floor", "2 floors", "3 floors", "4 or more floors");

			foreach($requirements as $category => $collection)
			{
				foreach($collection as $requirement)
				{
					$new = new Feature();
					$new->setName($requirement);
					$new->setCategory($this->getReference($category));

		            $errors = $validator->validate($new);

		            if ($errors->count())
		            {
	            		throw new \Exception("Validation error...", 100);
		            }

					$manager->persist($new);
					$manager->flush();

					if($category=="willing-to-adapt")
						$this->addReference("willing-".$slugify->slugify($requirement), $new);
					else
						$this->addReference($slugify->slugify($requirement), $new);

					unset($new);
				}
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
		return 170;
	}
}