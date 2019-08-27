<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\PropertyRiskAssessment;
use Cocur\Slugify\Slugify;

class LoadPropertyRiskAssessmentData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');
			$risks = array();

			// property 0
			$faker->seed(260100);
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-0", "answer"=>false, "comments"=>$faker->text(300), "level_of_risk"=>"Medium", "action_needed"=>true, "ticket"=>"ticket-0");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-1", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-2", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-3", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-4", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-5", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-6", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-7", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-8", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-9", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-10", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-11", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-12", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-13", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-14", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-15", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-16", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-0", "question"=>"risk-assessment-question-17", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");

			// property 1
			$faker->seed(260110);
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-0", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-1", "answer"=>false, "comments"=>$faker->text(300), "level_of_risk"=>"High", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-2", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-3", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-4", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-5", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-6", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-7", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-8", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-9", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-10", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-11", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-12", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-13", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-14", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-15", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-16", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-1", "question"=>"risk-assessment-question-17", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");

			// property 2
			$faker->seed(260120);
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-0", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-1", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-2", "answer"=>false, "comments"=>$faker->text(300), "level_of_risk"=>"Low", "action_needed"=>true, "ticket"=>"ticket-1");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-3", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-4", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-5", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-6", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-7", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-8", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-9", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-10", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-11", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-12", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-13", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-14", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-15", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-16", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");
			$risks[] = array("property"=>"property-2", "question"=>"risk-assessment-question-17", "answer"=>true, "comments"=>$faker->text(300), "level_of_risk"=>"", "action_needed"=>false, "ticket"=>"");

			foreach($risks as $key => $risk)
			{
				$entity = new PropertyRiskAssessment();
				$entity->setProperty($this->getReference($risk['property']));
				$entity->setQuestion($this->getReference($risk['question']));
				$entity->setAnswer($risk['answer']);
				$entity->setComments($risk['comments']);
				if($risk['level_of_risk'])
					$entity->setLevelOfRisk($risk['level_of_risk']);
				$entity->setActionNeeded($risk['action_needed']);

				if($risk['action_needed'])
				{
					$entity->setTicket($this->getReference($risk["ticket"]));
				}

	            $errors = $validator->validate($entity);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($entity);
				$manager->flush();

				$this->addReference("risk-assessment-".$key, $entity);
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
		return 260;
	}
}