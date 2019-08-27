<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\PropertyStatus;
use Cocur\Slugify\Slugify;

class LoadPropertyStatusData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$status = array("Available - New to LFL", "Fully occupied - LFL tenants", "Ineligible", "Available and void", "Rooms available", "Fully occupied - Not with LFL");

			$slugify = new Slugify();

			foreach($status as $key => $state)
			{
				$new = new PropertyStatus();
				$new->setName($state);

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("property-status-".$key, $new);

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
		return 190;
	}
}