<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\TypeHasStatus;

class LoadTypeHasStatusData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$types = array('type-landlord-investor' => array('unapproved', 'pending-approval', 'approved'),
						   'type-tenant' 		   => array('needs-housing', 'pending-housing', 'housed', 'ineligible'),
						   'type-contractor'        => array('unapproved', 'pending-approval', 'approved'),
						   'type-other'             => array('n-a'));

			foreach($types as $type => $statuses)
			{
				foreach ($statuses as $status)
				{
					$new = new TypeHasStatus();
					$new->setType($this->getReference($type));
					$new->setStatus($this->getReference($status));
		            $errors = $validator->validate($new);
		            if ($errors->count())
		            {
	            		throw new \Exception("Validation error...", 100);
		            }
					$manager->persist($new);
					$manager->flush();
					$this->addReference($type."-".$status, $new);
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
		return 35;
	}
}