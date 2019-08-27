<?php

namespace App\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\ApiBundle\Entity\Ticket;

class LoadTicketData extends AppBaseFixture
{
	public function load(ObjectManager $manager)
	{
		try
		{
			$validator = $this->container->get("validator");
			$faker = \Faker\Factory::create('en_GB');
			$faker->seed(500);
			$tickets = array();
			$tickets[] = array(
				'title'       => $faker->text(80),
				'description'      => $faker->text(450),
				'date_reported'      => $faker->date("d/m/Y"),
				'duedate_at'      => $faker->dateTimeBetween('now', '+1 year')->format("d/m/Y"),
				'status'    => "ticket-status-outstanding",
				'reported_by' => "tenant-0",
				'assign_to'   => "other-1",
				'ticket_type' => "ticket-type-key-replacement",
				'parent'      => "",
				'time_spent'  => "",
				'time_spent_unit'  => "",
				"read_by" => array(),
				"related_contacts" => array("landlord-0"),
				"related_properties" => array("property-0"),
			);

			$faker->seed(505);
			$tickets[] = array(
				'title'       => $faker->text(80),
				'description'      => $faker->text(450),
				'date_reported'      => $faker->date("d/m/Y"),
				'duedate_at'      => $faker->dateTimeBetween('now', '+1 year')->format("d/m/Y"),
				'status'    => "ticket-status-on-hold",
				'reported_by' => "",
				'assign_to'   => "",
				'ticket_type' => "ticket-type-safeguarding",
				'parent'      => "",
				'time_spent'  => 1,
				'time_spent_unit'  => "Hours",
				"read_by" => array("admin"),
				"related_contacts" => array("tenant-0"),
				"related_properties" => array("property-1"),
			);

			$faker->seed(510);
			$tickets[] = array(
				'title'       => $faker->text(80),
				'description'      => $faker->text(450),
				'date_reported'      => $faker->date("d/m/Y"),
				'duedate_at'      => $faker->dateTimeBetween('now', '+1 year')->format("d/m/Y"),
				'status'    => "ticket-status-completed",
				'reported_by' => "tenant-1",
				'assign_to'   => "",
				'ticket_type' => "ticket-type-financial",
				'parent'      => "",
				'time_spent'  => 30,
				'time_spent_unit'  => "Minutes",
				"read_by" => array("admin","cbennett"),
				"related_contacts" => array("landlord-1","tenant-2","other-0"),
				"related_properties" => array("property-0","property-1"),
			);

			$faker->seed(515);
			$tickets[] = array(
				'title'       => $faker->text(80),
				'description'      => $faker->text(450),
				'date_reported'      => $faker->date("d/m/Y"),
				'duedate_at'      => $faker->dateTimeBetween('now', '+1 year')->format("d/m/Y"),
				'status'    => "ticket-status-abandoned",
				'reported_by' => "",
				'assign_to'   => "other-2",
				'ticket_type' => "ticket-type-anti-social-behaviour",
				'parent'      => "ticket-0",
				'time_spent'  => 0.5,
				'time_spent_unit'  => "Hours",
				"read_by" => array(),
				"related_contacts" => array("landlord-0","landlord-1"),
				"related_properties" => array(),
			);

			$faker->seed(520);
			$tickets[] = array(
				'title'       => $faker->text(80),
				'description'      => $faker->text(450),
				'date_reported'      => $faker->date("d/m/Y"),
				'duedate_at'      => $faker->dateTimeBetween('now', '+1 year')->format("d/m/Y"),
				'status'    => "ticket-status-outstanding",
				'reported_by' => "",
				'assign_to'   => "",
				'ticket_type' => "ticket-type-miscellaneous",
				'parent'      => "ticket-3",
				'time_spent'  => 10.75,
				'time_spent_unit'  => "Hours",
				"read_by" => array("cbennett"),
				"related_contacts" => array(),
				"related_properties" => array(),
			);


			foreach($tickets as $key => $ticket)
			{
				$new = new Ticket();
				$new->setTitle($ticket['title']);
				$new->setDescription($ticket['description']);
				$new->setDateReported($ticket['date_reported']);
				$new->setDuedateAt($ticket['duedate_at']);
				$new->setStatus($this->getReference($ticket['status']));
				$new->setCreatedBy($this->getReference('admin'));
				$new->setUpdatedBy($this->getReference('admin'));

				if($ticket['reported_by'])
					$new->setReportedBy($this->getReference($ticket['reported_by']));

				if($ticket['assign_to'])
					$new->setAssignTo($this->getReference($ticket['assign_to']));

				if($ticket['ticket_type'])
					$new->setTicketType($this->getReference($ticket['ticket_type']));

				if($ticket['parent'])
					$new->setParent($this->getReference($ticket['parent']));

				if($ticket['time_spent'])
				{
					$new->setTimeSpent($ticket['time_spent']);
					$new->setTimeSpentUnit($ticket['time_spent_unit']);
				}

				foreach ($ticket['read_by']as $reader)
				{
					$new->addReadBy($this->getReference($reader));
				}

				foreach ($ticket['related_contacts'] as $contact)
				{
					$new->addRelatedContact($this->getReference($contact));
				}

				foreach ($ticket['related_properties'] as $property)
				{
					$new->addRelatedProperty($this->getReference($property));
				}

	            $errors = $validator->validate($new);
	            if ($errors->count())
	            {
            		throw new \Exception("Validation error...", 100);
	            }

				$manager->persist($new);
				$manager->flush();

				$this->addReference("ticket-$key", $new);

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
		return 220;
	}
}