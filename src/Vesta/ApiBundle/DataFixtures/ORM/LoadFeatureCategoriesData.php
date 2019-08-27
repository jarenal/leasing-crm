<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\FeatureCategories;
use Cocur\Slugify\Slugify;

class LoadFeatureCategoriesData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$categories = array('Property type', 'Bedrooms', 'Furnitures', 'Parking', 'Garden', 'Garden details', 'Accessibility', 'Smoking', 'Pets', 'Willing to adapt', 'Floors');
			$slugify = new Slugify();

			foreach($categories as $category)
			{
				$new = new FeatureCategories();
				$new->setName($category);

	            $errors = $validator->validate($new);

	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference($slugify->slugify($category), $new);

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
		return 160;
	}
}