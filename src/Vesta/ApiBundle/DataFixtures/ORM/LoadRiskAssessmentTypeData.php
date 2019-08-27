<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\RiskAssessmentType;
use Cocur\Slugify\Slugify;

class LoadRiskAssessmentTypeData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$slugify = new Slugify();
			$types = array();
			$types[] = array("name"=>"Property", "is_active"=>true);
			$types[] = array("name"=>"Tenant", "is_active"=>true);
			$types[] = array("name"=>"Landlord", "is_active"=>true);

			foreach($types as $type)
			{
				$entity = new RiskAssessmentType();
				$entity->setName($type['name']);
				$entity->setIsActive($type['is_active']);

	            $errors = $validator->validate($entity);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($entity);
				$manager->flush();

				$this->addReference("risk-assessment-type-".$slugify->slugify($type['name']), $entity);
				unset($entity);
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
		return 230;
	}
}