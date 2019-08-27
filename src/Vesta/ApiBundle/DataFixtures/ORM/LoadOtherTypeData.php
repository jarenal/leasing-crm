<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\OtherType;

class LoadOtherTypeData extends AppBaseFixture
{
    public function load(ObjectManager $manager)
    {
        try
        {
            $validator = $this->container->get("validator");
            $types = array('Family member', 'Support provider', 'Occupational therapist', 'Commissioner', 'Other', 'Social services', 'Trustees', 'Suppliers');

            foreach ($types as $key => $type) {
                $new = new OtherType();
                $new->setName($type);

                $errors = $validator->validate($new);
                if ($errors->count())
                {
                    throw new \Exception("Validation error...", 100);
                }

                $manager->persist($new);
                $manager->flush();

                $this->addReference("other-type-".$key, $new);

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
        return 90;
    }
}