<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\ContactStatus;
use Cocur\Slugify\Slugify;

class LoadContactStatusData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$states = array('Unapproved', 'Pending Approval', 'Approved', 'Needs housing', 'Pending housing', 'Housed', 'Ineligible', 'N/A');
			$slugify = new Slugify();

			foreach($states as $key => $state)
			{
				$new = new ContactStatus();
				$new->setName($state);

	            $errors = $validator->validate($new);

	            if ($errors->count())
	            {
	        		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference($slugify->slugify($state), $new);

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
		return 20;
	}
}