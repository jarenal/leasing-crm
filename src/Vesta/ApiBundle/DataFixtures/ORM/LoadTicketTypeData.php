<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\TicketType;
use Cocur\Slugify\Slugify;

class LoadTicketTypeData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$types = array('Anti-social behaviour','Key replacement','Complaints, Comms. & Comps','Viewings', 'Quality assurance', 'Safeguarding', 'Incidents and accidents', 'Financial', 'Miscellaneous', 'Repairs, Maint. & Adapt. (see repairs log)', 'Introduction');
			$slugify = new Slugify();

			foreach($types as $type)
			{
				$new = new TicketType();
				$new->setName($type);

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("ticket-type-".$slugify->slugify($type), $new);

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
		return 215;
	}
}