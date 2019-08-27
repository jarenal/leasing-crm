<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\RiskAssessmentQuestion;
use Cocur\Slugify\Slugify;

class LoadRiskAssessmentQuestionData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$questions = array();

			// Properties: Location & overview
			$questions[] = array("title"=>"Is the property on a bus route or near a train station?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Are there useful shops and amentities close by?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Is the Energy Performance Certificate D rating or above?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Is the property in a quiet area?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Is the area well-lit?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Is the property away from excessive traffic?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Is the property free from problems with pollution?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Does the area have low crime statistics?", "is_active"=>true, "category"=>"risk-assessment-category-0");
			$questions[] = array("title"=>"Does the property meet all criteria for the Decent Homes Standard?", "is_active"=>true, "category"=>"risk-assessment-category-0");

			// Properties: Exterior brickwork
			$questions[] = array("title"=>"Is it in good condition and free from cracks?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Is it relatively new?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Is it normal brickwork (no render or specific finish?)", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Are all tiles in position?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Are the chimneys straight?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Is the flashing (lead, makes external joins waterproof) secure?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Are drains and guttering in good condition?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"If it’s raining, are they free from any leaking?", "is_active"=>true, "category"=>"risk-assessment-category-1");
			$questions[] = array("title"=>"Are the fascias (wooden section under roof) in good condition?", "is_active"=>true, "category"=>"risk-assessment-category-1");

			// Tenants: Location & overview
			$questions[] = array("title"=>"Is the property on a bus route or near a train station?", "is_active"=>true, "category"=>"risk-assessment-category-11");
			$questions[] = array("title"=>"Are there useful shops and amentities close by?", "is_active"=>true, "category"=>"risk-assessment-category-11");

			// Tenants: Exterior brickwork
			$questions[] = array("title"=>"Is it in good condition and free from cracks?", "is_active"=>true, "category"=>"risk-assessment-category-12");
			$questions[] = array("title"=>"Is it relatively new?", "is_active"=>true, "category"=>"risk-assessment-category-12");

			// Landlords: Location & overview
			$questions[] = array("title"=>"Is the property on a bus route or near a train station?", "is_active"=>true, "category"=>"risk-assessment-category-13");
			$questions[] = array("title"=>"Are there useful shops and amentities close by?", "is_active"=>true, "category"=>"risk-assessment-category-13");

			// Landlords: Exterior brickwork
			$questions[] = array("title"=>"Is it in good condition and free from cracks?", "is_active"=>true, "category"=>"risk-assessment-category-14");
			$questions[] = array("title"=>"Is it relatively new?", "is_active"=>true, "category"=>"risk-assessment-category-14");

			/* Windows
			$questions[] = array("title"=>"Are they double glazed?", "is_active"=>true, "category"=>"risk-assessment-category-2");
			$questions[] = array("title"=>"Is it safety glass?", "is_active"=>true, "category"=>"risk-assessment-category-2");
			$questions[] = array("title"=>"Are they in good condition?", "is_active"=>true, "category"=>"risk-assessment-category-2");

			// Security
			$questions[] = array("title"=>"Are there good doors and locks?", "is_active"=>true, "category"=>"risk-assessment-category-3");
			$questions[] = array("title"=>"Are there good window locks?", "is_active"=>true, "category"=>"risk-assessment-category-3");
			$questions[] = array("title"=>"Is there a working alarm system?", "is_active"=>true, "category"=>"risk-assessment-category-3");
			*/

			foreach($questions as $key => $question)
			{
				$entity = new RiskAssessmentQuestion();
				$entity->setTitle($question['title']);
				$entity->setIsActive($question['is_active']);
				$entity->setCategory($this->getReference($question['category']));

	            $errors = $validator->validate($entity);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($entity);
				$manager->flush();

				$this->addReference("risk-assessment-question-".$key, $entity);
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
		return 250;
	}
}