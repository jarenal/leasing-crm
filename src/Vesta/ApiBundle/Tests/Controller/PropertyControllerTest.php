<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

$idproperty = null;

class PropertyControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newProperty;
	protected $editProperty;
	protected $emptyProperty;

	protected function setUp()
	{
		parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$this->fields = array('"message":"validation"',
								'landlord',
								'address',
								'postcode',
								'town',
								'local_authority',
								'availableDate',
								'targetRent',
								'status',
								);

		$this->routes = array("api_get_property"    => array("method" => "GET",    "uri" => "/api/properties/%s"),
							  "api_get_properties" => array("method" => "GET",    "uri" => "/api/properties"),
							  "api_post_property" => array("method" => "POST",   "uri" => "/api/properties"),
							  "api_post_property_update" => array("method" => "POST",    "uri" => "/api/properties/%s/updates"),
							  "api_delete_property" => array("method" => "DELETE", "uri" => "/api/properties/%s"),
							  );

		$faker->seed(400);
		$this->newProperty = array("property"=>array("landlord" => 1,
													"address" => $faker->streetAddress,
													"postcode" => $faker->postcode,
													"town" => $faker->city,
													"local_authority" => 1,
													"available_date" => $faker->date($format = 'Y-m-d', $max="now"),
													"parking_for" => 2,
													"special_design_features" => $faker->text(500),
													"previous_crimes_description" => $faker->text(300),
													"property_value" => $faker->randomFloat(2,0,9999999),
													"valuation_date" => $faker->date($format = 'Y-m-d', $max="now"),
													"target_rent" => $faker->randomFloat(2,0,9999),
													"mortgage_outstanding"=>1,
													"buy_to_let"=>0,
													"status" => 1,
													"features" => array(1,6,12,15,17,20,25,28,30),
		));

		$faker->seed(401);
		$this->editProperty = array("property"=>array("landlord" => 1,
													"address" => $faker->streetAddress,
													"postcode" => $faker->postcode,
													"town" => $faker->city,
													"local_authority" => 1,
													"available_date" => $faker->date($format = 'Y-m-d', $max="now"),
													"parking_for" => 2,
													"special_design_features" => $faker->text(500),
													"previous_crimes_description" => $faker->text(300),
													"property_value" => $faker->randomFloat(2,0,9999999),
													"valuation_date" => $faker->date($format = 'Y-m-d', $max="now"),
													"target_rent" => $faker->randomFloat(2,0,9999),
													"mortgage_outstanding"=>0,
													"buy_to_let"=>1,
													"status" => 1,
													"features" => array(2,7,13,16,18,22,26,29,31),
		));

		$this->emptyProperty = array("property"=>array());

	}

	public function testCgetAction()
	{

		$this->client->request($this->routes['api_get_properties']['method'],
						 $this->routes['api_get_properties']['uri']);

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
		$this->client->request($this->routes['api_post_property']['method'],
						 $this->routes['api_post_property']['uri']);

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
		$this->client->request($this->routes['api_post_property']['method'],
						 $this->routes['api_post_property']['uri'],
						 array('property' => array('address'=>'Lorem ipsum')));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"address"',
			$this->client->getResponse()->getContent()
		);

		// Insert new record
		/* TEST 3 */
		$this->client->request($this->routes['api_post_property']['method'],
						 $this->routes['api_post_property']['uri'],
						 $this->newProperty);

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

		$GLOBALS['idproperty'] = $responseObj->data->id;
	}

	public function testPostUpdateAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_post_property_update']['method'],
						 sprintf($this->routes['api_post_property_update']['uri'], $GLOBALS['idproperty']),
						 $this->emptyProperty);

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
		$this->emptyProperty['property']['address'] = "Lorem ipsum...";

		$this->client->request($this->routes['api_post_property_update']['method'],
						 sprintf($this->routes['api_post_property_update']['uri'], $GLOBALS['idproperty']),
						 $this->emptyProperty);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"address"',
			$this->client->getResponse()->getContent()
		);

		/* TEST 3 */
		// update record
		$this->client->request($this->routes['api_post_property_update']['method'],
						 sprintf($this->routes['api_post_property_update']['uri'], $GLOBALS['idproperty']),
						 $this->editProperty);

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

	public function testGetAction()
	{
		$this->client->request($this->routes['api_get_property']['method'],
						 sprintf($this->routes['api_get_property']['uri'], $GLOBALS['idproperty']));

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

		/* TEST 2 */
		$this->client->request($this->routes['api_get_property']['method'],
						 sprintf($this->routes['api_get_property']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The property \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		// TEST 1
		$this->client->request($this->routes['api_delete_property']['method'],
						 $this->routes['api_delete_property']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idproperty is required", $responseObj->message);

		// TEST 2
		$this->client->request($this->routes['api_delete_property']['method'],
						 sprintf($this->routes['api_delete_property']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The property \"100\" doesn't exist.", $responseObj->message);

		// TEST 3
		$this->client->request($this->routes['api_delete_property']['method'],
						 sprintf($this->routes['api_delete_property']['uri'], $GLOBALS['idproperty']));

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