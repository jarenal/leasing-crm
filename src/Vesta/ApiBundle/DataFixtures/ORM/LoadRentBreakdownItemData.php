<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\RentBreakdownItem;

class LoadRentBreakdownItemData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$items = array();
			$items[] = array(
				'is_active'   => true,
				'name'        => "Core lease charge",
				'description' => "Includes sinking fund, adaptations, development costs, fair wear and tear, buildings insurance, provision of furniture and white goods. council tax, where applicable",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Property related management",
				'description' => "This is the cost of the housing management relevant to the provision of the accommodation, not the provision of support. A breakdown of the total housing management costs and whats included, split between property and support, should be provided with supporting evidence.",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Replacement of white goods",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Replacement of furniture, flooring, curtains for tenant areas",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Repairs and Maintenance for excessive wear and tear",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Maintenance/Servicing for fire fighting equipment",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Maintenance/Servicing for heating equipment",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Maintenace/Servicing of eletrical system",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Maintenace/Servicing of thermostatic heat valves/water",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Maintenance/Servicing contracts of property equipment",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Refuse disposal and pest control",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Grounds and garden maintenance",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Door entry and CCTV security systems",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Communal utilities",
				'description' => "This should not include fuel and utilities consumed privatly by the individual tenant as the HB Applicable Amount covers the fuel used for the tenants own cooking and cleaning, whether those activities take place in private or shared facilities.",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "Communal telephone",
				'description' => "",
			);
			$items[] = array(
				'is_active'   => true,
				'name'        => "External window cleaning",
				'description' => "",
			);

			foreach($items as $key => $item)
			{
				$new = new RentBreakdownItem();
				$new->setIsActive($item['is_active']);
				$new->setName($item['name']);
				$new->setDescription($item['description']);

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

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
		return 270;
	}
}