<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\TenantCondition;

class LoadTenantConditionData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$conditions = array();

			$conditions[] = array("name"=>"Physical disability and require the following aids to support them:", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Frame", "parent"=>"condition-0", "is_other"=>false);
			$conditions[] = array("name"=>"Hoist", "parent"=>"condition-0", "is_other"=>false);
			$conditions[] = array("name"=>"Wheelchair", "parent"=>"condition-0", "is_other"=>false);

			$conditions[] = array("name"=>"Autism", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Sensory impaired, affecting the following senses:", "parent"=>"", "is_other"=>false);

			$conditions[] = array("name"=>"Sight", "parent"=>"condition-5", "is_other"=>false);
			$conditions[] = array("name"=>"Sound", "parent"=>"condition-5", "is_other"=>false);
			$conditions[] = array("name"=>"Balance", "parent"=>"condition-5", "is_other"=>false);
			$conditions[] = array("name"=>"Other, please state", "parent"=>"condition-5", "is_other"=>true);

			$conditions[] = array("name"=>"Display behaviours that challenge", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Learning disability", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Mental health issues", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Dementia", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Epilepsy", "parent"=>"", "is_other"=>false);
			$conditions[] = array("name"=>"Other, please state", "parent"=>"", "is_other"=>true);


			foreach($conditions as $key => $item)
			{
				$new = new TenantCondition();
				$new->setName($item['name']);
				$new->setIsOther($item['is_other']);

				if($item['parent'])
				{
					$new->setParent($this->getReference($item['parent']));
				}

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("condition-$key", $new);

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
		return 140;
	}
}