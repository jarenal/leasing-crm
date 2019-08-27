<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

$idticket = null;

class TicketControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newTicket;
	protected $editTicket;
	protected $emptyTicket;

	protected function setUp()
	{
		parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$this->fields = array('"message":"validation"',
								'title',
								'description',
								'status',
								'ticket_type');

		$this->routes = array("api_get_ticket"    => array("method" => "GET",    "uri" => "/api/tickets/%s"),
							  "api_get_tickets"   => array("method" => "GET",    "uri" => "/api/tickets"),
							  "api_post_ticket"   => array("method" => "POST",   "uri" => "/api/tickets"),
							  "api_put_ticket"    => array("method" => "PUT",    "uri" => "/api/tickets/%s"),
							  "api_delete_ticket" => array("method" => "DELETE", "uri" => "/api/tickets/%s"),
							  "api_get_tickets_combobox" => array("method" => "GET", "uri" => "/api/tickets/combobox"),
							  );

		$faker->seed(502);
		$this->newTicket = array("ticket"=>array("title" => $faker->text(50),
			"description" => $faker->text(500),
			"duedate_at" => "",
			"status" => 1,
			"reported_by" => "",
			"assign_to" => "",
			"ticket_type" => 1,
			"parent" => "",
			"time_spent" => $faker->randomFloat(2,0,999),
			"time_spent_unit" => "",
			"related_contacts" => array(1,2,3),
			"related_properties" => array(1,3),
		));

		$faker->seed(505);
		$this->editTicket = array("ticket"=>array("title" => $faker->text(70),
			"description" => $faker->text(200),
			"duedate_at" => $faker->dateTimeBetween("now", "+1 year")->format("Y-m-d H:i:s"),
			"status" => 2,
			"reported_by" => 2,
			"assign_to" => 8,
			"ticket_type" => 4,
			"parent" => 1,
			"time_spent" => $faker->randomFloat(2,0,999),
			"time_spent_unit" => "Hours",
			"related_contacts" => array(1,3,4),
			"related_properties" => array(2),
		));

		$this->emptyTicket = array("ticket"=>array("title" => "",
			"description" => "",
			"duedate_at" => "",
			"status" => "",
			"reported_by" => "",
			"assign_to" => "",
			"ticket_type" => "",
			"parent" => "",
			"time_spent" => "",
			"time_spent_unit" => "",
			"related_contacts" => array(),
			"related_properties" => array(),
		));

		$faker->seed(510);
		$this->ticketPropertyNotExist = array("ticket"=>array("title" => $faker->text(50),
			"description" => $faker->text(500),
			"duedate_at" => "",
			"status" => 1,
			"reported_by" => "",
			"assign_to" => "",
			"ticket_type" => 1,
			"parent" => "",
			"time_spent" => $faker->randomFloat(2,0,999),
			"time_spent_unit" => "",
			"related_contacts" => array(1,2,3),
			"related_properties" => array(400),
		));

		$faker->seed(520);
		$this->ticketContactNotExist = array("ticket"=>array("title" => $faker->text(50),
			"description" => $faker->text(500),
			"duedate_at" => "",
			"status" => 1,
			"reported_by" => "",
			"assign_to" => "",
			"ticket_type" => 1,
			"parent" => "",
			"time_spent" => $faker->randomFloat(2,0,999),
			"time_spent_unit" => "",
			"related_contacts" => array(400),
			"related_properties" => array(1,2),
		));

	}

	public function testCgetAction()
	{

		$this->client->request($this->routes['api_get_tickets']['method'],
						 $this->routes['api_get_tickets']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());

		$this->assertEquals("success", $responseObj->message);

	}

	public function testPostAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_post_ticket']['method'],
						 $this->routes['api_post_ticket']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// is working the validation?
		foreach ($this->fields as $field) {
			$this->assertContains(
				$field,
				$this->client->getResponse()->getContent()
			);
		}

		$client_response = json_decode($this->client->getResponse()->getContent());
		$total_api_validation_fields = count($client_response->errors);
		$total_test_validation_fields = count($this->fields)-1;
		$this->assertEquals($total_api_validation_fields, $total_test_validation_fields, "The validation fields don't match.");

		/* TEST 2 */
		$this->client->request($this->routes['api_post_ticket']['method'],
						 $this->routes['api_post_ticket']['uri'],
						 array('ticket' => array('title'=>'bla bla bla')));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"title"',
			$this->client->getResponse()->getContent()
		);

		// Insert new record
		/* TEST 3 */
		$this->client->request($this->routes['api_post_ticket']['method'],
						 $this->routes['api_post_ticket']['uri'],
						 $this->newTicket);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());

		$this->assertEquals("success", $responseObj->message, $this->client->getResponse()->getContent());

		$GLOBALS['idticket'] = $responseObj->data->id;
	}

	public function testPropertyNotExistAction()
	{
		/* TEST 3 */
		$this->client->request($this->routes['api_post_ticket']['method'],
						 $this->routes['api_post_ticket']['uri'],
						 $this->ticketPropertyNotExist);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());

		$this->assertEquals("The property with id \"400\" doesn't exist.", $responseObj->message, $this->client->getResponse()->getContent());
	}

	public function testContactNotExistAction()
	{
		/* TEST 3 */
		$this->client->request($this->routes['api_post_ticket']['method'],
						 $this->routes['api_post_ticket']['uri'],
						 $this->ticketContactNotExist);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());

		$this->assertEquals("The contact with id \"400\" doesn't exist.", $responseObj->message, $this->client->getResponse()->getContent());
	}

	public function testPutAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_put_ticket']['method'],
						 sprintf($this->routes['api_put_ticket']['uri'], $GLOBALS['idticket']),
						 $this->emptyTicket);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		foreach ($this->fields as $field) {
			$this->assertContains(
				$field,
				$this->client->getResponse()->getContent()
			);
		}

		$client_response = json_decode($this->client->getResponse()->getContent());
		$total_api_validation_fields = count($client_response->errors);
		$total_test_validation_fields = count($this->fields)-1;
		$this->assertEquals($total_api_validation_fields, $total_test_validation_fields, "The validation fields don't match.");

		/* TEST 2 */
		$this->emptyTicket['ticket']['title'] = "bla bla bla";

		$this->client->request($this->routes['api_put_ticket']['method'],
						 sprintf($this->routes['api_put_ticket']['uri'], $GLOBALS['idticket']),
						 $this->emptyTicket);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"title"',
			$this->client->getResponse()->getContent()
		);

		/* TEST 3 */
		// update record
		$this->client->request($this->routes['api_put_ticket']['method'],
						 sprintf($this->routes['api_put_ticket']['uri'], $GLOBALS['idticket']),
						 $this->editTicket);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("success", $responseObj->message);
	}

	public function testGetAction()
	{
		$this->client->request($this->routes['api_get_ticket']['method'],
						 sprintf($this->routes['api_get_ticket']['uri'], $GLOBALS['idticket']));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());

		$this->assertEquals("success", $responseObj->message);

		/* TEST 2 */
		$this->client->request($this->routes['api_get_ticket']['method'],
						 sprintf($this->routes['api_get_ticket']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The task \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		// TEST 1
		$this->client->request($this->routes['api_delete_ticket']['method'],
						 $this->routes['api_delete_ticket']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idticket is required", $responseObj->message);

		// TEST 2
		$this->client->request($this->routes['api_delete_ticket']['method'],
						 sprintf($this->routes['api_delete_ticket']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The task \"100\" doesn't exist.", $responseObj->message);

		// TEST 3
		$this->client->request($this->routes['api_delete_ticket']['method'],
						 sprintf($this->routes['api_delete_ticket']['uri'], $GLOBALS['idticket']));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("success", $responseObj->message);
	}

	public function testCgetComboboxAction()
	{
		$this->client->request($this->routes['api_get_tickets_combobox']['method'],
						 $this->routes['api_get_tickets_combobox']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

}