<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

$idcontact = null;

class OtherControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newContact;
	protected $editContact;
	protected $emptyContact;

	protected function setUp()
	{
		parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$this->fields = array('"message":"validation"',
								'name',
								'surname',
								'mobile',
								'address',
								'postcode',
								'town',
								'contact_title',
								'status',
								'other_type');

		$this->routes = array("api_get_other"    => array("method" => "GET",    "uri" => "/api/others/%s"),
							  "api_get_others"   => array("method" => "GET",    "uri" => "/api/others"),
							  "api_post_other"   => array("method" => "POST",   "uri" => "/api/others"),
							  "api_put_other"    => array("method" => "PUT",    "uri" => "/api/others/%s"),
							  "api_delete_other" => array("method" => "DELETE", "uri" => "/api/others/%s"),
							  "api_get_others_combobox"   => array("method" => "GET",    "uri" => "/api/others/combobox"),
							  );

		$faker->seed(302);
		$this->newContact = array("other"=>array("contact_type" => 4,
			"other_type" => 6,
			"contact_title" => 9,
			"name" => $faker->firstName,
			"surname" => $faker->lastName,
			"email" => $faker->email,
			"landline" => $faker->phoneNumber,
			"mobile" => $faker->phoneNumber,
			"address" => $faker->streetAddress,
			"postcode" => $faker->postcode,
			"town" => $faker->city,
			'contact_status' => 8,
			"contact_method" => "1",
			"contact_method_other" => "",
		));

		$faker->seed(305);
		$this->editContact = array("other"=>array("contact_type" => 4,
			"other_type" => 1,
			"contact_title" => 7,
			"name" => $faker->firstName,
			"surname" => $faker->lastName,
			"email" => $faker->email,
			"landline" => $faker->phoneNumber,
			"mobile" => $faker->phoneNumber,
			"address" => $faker->streetAddress,
			"postcode" => $faker->postcode,
			"town" => $faker->city,
			'contact_status' => 8,
			"contact_method" => "4",
			"contact_method_other" => "Smoke signals",
		));

		$this->emptyContact = array("other"=>array("contact_type" => "",
			"contact_title" => "",
			"name" => "",
			"surname" => "",
			"email" => "",
			"landline" => "",
			"mobile" => "",
			"address" => "",
			"postcode" => "",
			"town" => "",
			"contact_status" => "",
			"contact_method" => "",
			"contact_method_other" => "",
		));

	}

	public function testCgetComboboxAction()
	{
		$this->client->request($this->routes['api_get_others_combobox']['method'],
						 $this->routes['api_get_others_combobox']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

	public function testCgetAction()
	{

		$this->client->request($this->routes['api_get_others']['method'],
						 $this->routes['api_get_others']['uri']);

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
		$this->client->request($this->routes['api_post_other']['method'],
						 $this->routes['api_post_other']['uri']);

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
		$this->assertEquals($total_api_validation_fields, $total_test_validation_fields, "The validation fields don't match: ".$this->client->getResponse()->getContent());

		/* TEST 2 */
		$this->client->request($this->routes['api_post_other']['method'],
						 $this->routes['api_post_other']['uri'],
						 array('other' => array('name'=>'Pepe Luis')));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"name"',
			$this->client->getResponse()->getContent()
		);

		// Insert new record
		/* TEST 3 */
		$this->client->request($this->routes['api_post_other']['method'],
						 $this->routes['api_post_other']['uri'],
						 $this->newContact);

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

		$GLOBALS['idcontact'] = $responseObj->data->id;
	}

	public function testPutAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_put_other']['method'],
						 sprintf($this->routes['api_put_other']['uri'], $GLOBALS['idcontact']),
						 $this->emptyContact);

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
		$this->emptyContact['other']['name'] = "Pepe Luis";

		$this->client->request($this->routes['api_put_other']['method'],
						 sprintf($this->routes['api_put_other']['uri'], $GLOBALS['idcontact']),
						 $this->emptyContact);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// is working the validation?
		$this->assertNotContains(
			'"name"',
			$this->client->getResponse()->getContent()
		);

		/* TEST 3 */
		// update record
		$this->client->request($this->routes['api_put_other']['method'],
						 sprintf($this->routes['api_put_other']['uri'], $GLOBALS['idcontact']),
						 $this->editContact);

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
		$this->client->request($this->routes['api_get_other']['method'],
						 sprintf($this->routes['api_get_other']['uri'], $GLOBALS['idcontact']));

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
		$this->client->request($this->routes['api_get_other']['method'],
						 sprintf($this->routes['api_get_other']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The contact \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		// TEST 1
		$this->client->request($this->routes['api_delete_other']['method'],
						 $this->routes['api_delete_other']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idcontact is required", $responseObj->message);

		// TEST 2
		$this->client->request($this->routes['api_delete_other']['method'],
						 sprintf($this->routes['api_delete_other']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The contact \"100\" doesn't exist.", $responseObj->message);

		// TEST 3
		$this->client->request($this->routes['api_delete_other']['method'],
						 sprintf($this->routes['api_delete_other']['uri'], $GLOBALS['idcontact']));

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