<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

$idcontractor = null;

class ContractorControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newContractor;
	protected $editContractor;
	protected $emptyContractor;

	protected function setUp()
	{
		parent::setUp();

		$this->fields = array('"message":"validation"',
								'name',
								'surname',
								'mobile',
								'address',
								'postcode',
								'town',
								'status',
								'contact_title',
								'areas',
								'services');

		$this->routes = array("api_get_contractor"    => array("method" => "GET",    "uri" => "/api/contractors/%s"),
							  "api_get_contractors"   => array("method" => "GET",    "uri" => "/api/contractors"),
							  "api_post_contractor"   => array("method" => "POST",   "uri" => "/api/contractors"),
							  "api_post_contractor_update"    => array("method" => "POST",    "uri" => "/api/contractors/%s/updates"),
							  "api_delete_contractor" => array("method" => "DELETE", "uri" => "/api/contractors/%s"),
							  );

		$this->newContractor = array("contractor"=>array("contact_type" => 3,
			"contact_title" => 1,
			"organisation" => 1,
			"name" => "Contractor",
			"surname" => "Test",
			"email" => "antonio.test@example.com",
			"landline" => "01625536709",
			"mobile" => "07957574495",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"contact_status" => 1,
			"require_certification" => 0,
			"liability_insurance" => 0,
			"services" => array(2,4,6),
			"areas" => array(array("distance"=>5,
							 "postcode" => "W1S"),
						array("distance"=>10,
										 "postcode" => "W1S")),
			"contact_method" => "1",
			"contact_method_other" => "",
		));

		$this->editContractor = array("contractor"=>array("contact_type" => 3,
			"contact_title" => 1,
			"organisation" => 1,
			"name" => "Contractor2",
			"surname" => "Test2",
			"email" => "antonio.test2@example.com",
			"landline" => "01625536709",
			"mobile" => "07957574495",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"contact_status" => 1,
			"require_certification" => 1,
			"liability_insurance" => 1,
			"services" => array(7,9),
			"areas" => array(array("id"=>1,
								   "distance"=>50,
							 	   "postcode" => "W1S"),
							 array("distance"=>20,
								   "postcode" => "W1S")),
			"contact_method" => "4",
			"contact_method_other" => "Smoke signals",
		));

		$this->emptyContractor = array("contractor"=>array("contact_type" => "",
			"contact_title" => "",
			"organisation" => "",
			"name" => "",
			"surname" => "",
			"email" => "",
			"landline" => "",
			"mobile" => "",
			"address" => "",
			"postcode" => "",
			"town" => "",
			"contact_status" => "",
			"require_certification" => "",
			"liability_insurance" => "",
			"contact_method" => "",
			"contact_method_other" => "",
		));
	}

	public function testCgetAction()
	{
		$this->client->request($this->routes['api_get_contractors']['method'],
						 $this->routes['api_get_contractors']['uri']);

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

	public function testPostAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_post_contractor']['method'],
						 $this->routes['api_post_contractor']['uri']);

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
		$this->client->request($this->routes['api_post_contractor']['method'],
						 $this->routes['api_post_contractor']['uri'],
						 array('contractor' => array('name'=>'Federico')));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// is working the validation?
		$this->assertNotContains(
			'"name"',
			$this->client->getResponse()->getContent()
		);

		// Insert new record
		/* TEST 3 */
		$this->client->request($this->routes['api_post_contractor']['method'],
						 $this->routes['api_post_contractor']['uri'],
						 $this->newContractor);

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

		$GLOBALS['idcontractor'] = $responseObj->data->id;
	}

	public function testPostUpdateAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_post_contractor_update']['method'],
						 sprintf($this->routes['api_post_contractor_update']['uri'], $GLOBALS['idcontractor']),
						 $this->emptyContractor);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
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
		$this->emptyContractor['contractor']['name'] = "Juan Antonio";

		$this->client->request($this->routes['api_post_contractor_update']['method'],
						 sprintf($this->routes['api_post_contractor_update']['uri'], $GLOBALS['idcontractor']),
						 $this->emptyContractor);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// is working the validation?
		$this->assertNotContains(
			'"name"',
			$this->client->getResponse()->getContent()
		);

		/* TEST 3 */
		// update record
		$this->client->request($this->routes['api_post_contractor_update']['method'],
						 sprintf($this->routes['api_post_contractor_update']['uri'], $GLOBALS['idcontractor']),
						 $this->editContractor);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json'), $this->client->getResponse()->getContent()
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), $this->client->getResponse()->getContent());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("success", $responseObj->message, $this->client->getResponse()->getContent());
	}

	public function testGetAction()
	{
		$this->client->request($this->routes['api_get_contractor']['method'],
						 sprintf($this->routes['api_get_contractor']['uri'], $GLOBALS['idcontractor']));

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
		$this->client->request($this->routes['api_get_contractor']['method'],
						 sprintf($this->routes['api_get_contractor']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The contractor \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_delete_contractor']['method'],
						 $this->routes['api_delete_contractor']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idcontractor is required", $responseObj->message);

		/* TEST 2 */
		$this->client->request($this->routes['api_delete_contractor']['method'],
						 sprintf($this->routes['api_delete_contractor']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The contractor \"100\" doesn't exist.", $responseObj->message);

		/* TEST 3 */
		$this->client->request($this->routes['api_delete_contractor']['method'],
						 sprintf($this->routes['api_delete_contractor']['uri'], $GLOBALS['idcontractor']));

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

}