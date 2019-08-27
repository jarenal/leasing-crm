<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\TenantArea;

class LoadTenantAreaData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$areas = array();
			$areas[] = array('postcode' => 'M9 4GB',
							 'distance' => 5);
			$areas[] = array('postcode' => 'RH6 0EP',
							 'distance' => 10);
			$areas[] = array('postcode' => 'TW12 1AQ',
							 'distance' => 15);
			$areas[] = array('postcode' => 'MK10 9AP',
							 'distance' => 20);
			$areas[] = array('postcode' => 'LS11 6AN',
							 'distance' => 5);
			$areas[] = array('postcode' => 'CR6 9HN',
							 'distance' => 5);
			$areas[] = array('postcode' => 'RM13 9YG',
							 'distance' => 10);
			$areas[] = array('postcode' => 'LS14 1BT',
							 'distance' => 15);
			$areas[] = array('postcode' => 'BT27 4PR',
							 'distance' => 20);
			$areas[] = array('postcode' => 'BA16 0LP',
							 'distance' => 5);
			$areas[] = array('postcode' => 'EH26 0JS',
							 'distance' => 10);
			$areas[] = array('postcode' => 'LL32 8SD',
							 'distance' => 15);
			$areas[] = array('postcode' => 'CM99 2DN',
							 'distance' => 20);

			foreach($areas as $key => $area)
			{
				$new = new TenantArea();
				$new->setPostcode($area['postcode']);
				$new->setDistance($area['distance']);

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("tenant-area-".$key, $new);

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
		return 150;
	}
}