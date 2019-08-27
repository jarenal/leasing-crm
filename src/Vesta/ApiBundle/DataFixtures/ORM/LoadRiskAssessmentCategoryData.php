<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\RiskAssessmentCategory;
use Cocur\Slugify\Slugify;

class LoadRiskAssessmentCategoryData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$categories = array();
			$categories[] = array("name"=>"Location & overview", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Exterior brickwork", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Roof", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Windows", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Security", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"What to test", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Inside the house (each room)", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"In the bathroom", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"In the garden", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Questions to ask", "is_active"=>true, "type"=>"risk-assessment-type-property");
			$categories[] = array("name"=>"Extra questions for flats", "is_active"=>true, "type"=>"risk-assessment-type-property");

			$categories[] = array("name"=>"Location & overview", "is_active"=>true, "type"=>"risk-assessment-type-tenant");
			$categories[] = array("name"=>"Exterior brickwork", "is_active"=>true, "type"=>"risk-assessment-type-tenant");

			$categories[] = array("name"=>"Location & overview", "is_active"=>true, "type"=>"risk-assessment-type-landlord");
			$categories[] = array("name"=>"Exterior brickwork", "is_active"=>true, "type"=>"risk-assessment-type-landlord");

			foreach($categories as $key => $category)
			{
				$entity = new RiskAssessmentCategory();
				$entity->setName($category['name']);
				$entity->setIsActive($category['is_active']);
				$entity->setType($this->getReference($category['type']));

	            $errors = $validator->validate($entity);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($entity);
				$manager->flush();

				$this->addReference("risk-assessment-category-".$key, $entity);
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
		return 240;
	}
}