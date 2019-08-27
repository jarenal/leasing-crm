<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\ContactTitle;

class LoadContactTitleData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$titles = array('Mr','Miss','Mrs','Ms','Canon','Cllr','Dr','Lady','Prof','Rev','Sir','Sister');

			foreach($titles as $title)
			{
				$new = new ContactTitle();
				$new->setName($title);

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference($title, $new);

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
		return 10;
	}
}