<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;
use App\ApiBundle\Utils\FakeNinGenerator;

$idtenant = null;

class TenantControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newTenant;
	protected $editTenant;
	protected $emptyTenant;

	protected function setUp()
	{
		parent::setUp();

		$faker = \Faker\Factory::create('en_GB');
		$fakeNinGenerator = new FakeNinGenerator();

		$this->fields = array('"message":"validation"',
								'name',
								'surname',
								'mobile',
								'address',
								'postcode',
								'town',
								'contact_title',
								'birthdate',
								'gender',
								'marital_status',
								'nin',
								'local_authority',
								'status');

		$this->routes = array("api_get_tenant"    => array("method" => "GET",    "uri" => "/api/tenants/%s"),
							  "api_get_tenants"   => array("method" => "GET",    "uri" => "/api/tenants"),
							  "api_post_tenant"   => array("method" => "POST",   "uri" => "/api/tenants"),
							  "api_put_tenant"    => array("method" => "PUT",    "uri" => "/api/tenants/%s"),
							  "api_delete_tenant" => array("method" => "DELETE", "uri" => "/api/tenants/%s"),
							  );

		$faker->seed(200);

		$child1 = array(
			"name" => $faker->firstName()." ".$faker->lastName(),
			"birthdate" => $faker->date($format = 'Y-m-d', $max = '18 years ago'),
			"guardianship" => rand(0,1)
		);

		$faker->seed(201);

		$child2 = array(
			"name" => $faker->firstName()." ".$faker->lastName(),
			"birthdate" => $faker->date($format = 'Y-m-d', $max = '18 years ago'),
			"guardianship" => rand(0,1)
		);

		$faker->seed(202);
		$this->newTenant = array("tenant"=>array("contact_type" => 2,
			"contact_title" => 9,
			"name" => $faker->firstName,
			"surname" => $faker->lastName,

			"email" => $faker->email,
			"landline" => $faker->phoneNumber,
			"mobile" => $faker->phoneNumber,

			"address" => $faker->streetAddress,
			"postcode" => $faker->postcode,
			"town" => $faker->city,

			"birthdate" => $faker->date($format = 'Y-m-d', $max = '18 years ago'),
			"gender" => rand(1,4),
			"marital_status" => rand(1,6),
			"nin" => $fakeNinGenerator->generateNINO(),
			"local_authority" => 1,
			"social_services_contact" => 7,

			"children" => array($child1,$child2),

			"need_night_support" => rand(0,1),
			"nights_support" => array(1,2),
			"has_chc_budget" => rand(0,1),
			"support_package_hours" => 1000,
			"conditions" => array(1,2,3,6,10),
			"other_10" => "Other conditions...",
			"agency_support_provider" => 3,
			"contact_support_provider" => 7,

			"deputy" => 8,

			"housingRegister" => rand(0,1),
			"moveDate" => $faker->date($format = 'Y-m-d', $max = 'now'),
			"areas" => array(array("distance"=>5,
							 "postcode" => "W1S"),
						array("distance"=>10,
										 "postcode" => "W1S")),
			"outCounty" => rand(0,1),
			"specialDesignFeatures" => $faker->text(500),
			"tenantPersonality" => $faker->text(250),
			"willingToShare" => rand(0,1),
			"parkingFor" => 2,

			"drugHistorial" => rand(0,1),
			"drugHistorialDetails" => $faker->text(300),
			"sexualOffencesHistorial" => rand(0,1),
			"sexualOffencesHistorialDetails" => $faker->text(1000),
			"arsonHistorial" => rand(0,1),
			"arsonHistorialDetails" => $faker->text(1500),
			"evictionsHistorial" => rand(0,1),
			"evictionsHistorialDetails" => $faker->text(100),
			"tenantReferences" => $faker->text(10000),

			'contact_status' => 4,
			"contact_method" => "1",
			"contact_method_other" => "",
			"property" => "",
		));

		$faker->seed(203);
		$child1 = array_merge(array("id"=>1),$child1);
		$child1['name'] = $faker->firstName()." ".$faker->lastName();
		$child1['birthdate'] = $faker->date($format = 'Y-m-d', $max = '18 years ago');
		$child1['guardianship'] = rand(0,1);

		$faker->seed(204);
		$child3['name'] = $faker->firstName()." ".$faker->lastName();
		$child3['birthdate'] = $faker->date($format = 'Y-m-d', $max = '18 years ago');
		$child3['guardianship'] = rand(0,1);

		$faker->seed(205);
		$this->editTenant = array("tenant"=>array("contact_type" => 2,
			"contact_title" => 7,
			"name" => $faker->firstName,
			"surname" => $faker->lastName,

			"email" => $faker->email,
			"landline" => $faker->phoneNumber,
			"mobile" => $faker->phoneNumber,

			"address" => $faker->streetAddress,
			"postcode" => $faker->postcode,
			"town" => $faker->city,

			"birthdate" => $faker->date($format = 'Y-m-d', $max = '18 years ago'),
			"gender" => rand(1,4),
			"marital_status" => rand(1,6),
			"nin" => $fakeNinGenerator->generateNINO(),
			"local_authority" => 1,
			"social_services_contact" => 8,

			"children" => array($child1,$child3),

			"need_night_support" => rand(0,1),
			"nights_support" => array(2),
			"has_chc_budget" => rand(0,1),
			"support_package_hours" => 2500,
			"conditions" => array(1,3,6),
			"agency_support_provider" => 2,
			"contact_support_provider" => 8,

			"deputy" => 7,

			"housingRegister" => rand(0,1),
			"moveDate" => $faker->date($format = 'Y-m-d', $max = 'now'),
			"areas" => array(array("id"=>1,
								   "distance"=>50,
							 	   "postcode" => $faker->postcode),
							 array("distance"=>20,
								   "postcode" => $faker->postcode)),
			"outCounty" => rand(0,1),
			"specialDesignFeatures" => $faker->text(500),
			"tenantPersonality" => $faker->text(250),
			"willingToShare" => rand(0,1),
			"parkingFor" => 4,

			"drugHistorial" => rand(0,1),
			"drugHistorialDetails" => $faker->text(300),
			"sexualOffencesHistorial" => rand(0,1),
			"sexualOffencesHistorialDetails" => $faker->text(1000),
			"arsonHistorial" => rand(0,1),
			"arsonHistorialDetails" => $faker->text(1500),
			"evictionsHistorial" => rand(0,1),
			"evictionsHistorialDetails" => $faker->text(100),
			"tenantReferences" => $faker->text(10000),

			'contact_status' => 5,
			"contact_method" => "4",
			"contact_method_other" => "Smoke signals",
			"property" => 1,
		));

		$this->emptyTenant = array("tenant"=>array("contact_type" => "",
			"contact_title" => "",
			"name" => "",
			"surname" => "",

			"email" => "",
			"landline" => "",
			"mobile" => "",

			"address" => "",
			"postcode" => "",
			"town" => "",

			"birthdate" => "",
			"gender" => "",
			"marital_status" => "",
			"nin" => "",
			"local_authority" => "",
			"social_services_contact" => "",

			"children" => array(),

			"need_night_support" => "",
			"nights_support" => "",
			"has_chc_budget" => "",
			"support_package_hours" => "",
			"conditions" => array(),
			"agency_support_provider" => "",
			"contact_support_provider" => "",

			"deputy" => "",

			"housingRegister" => "",
			"moveDate" => "",
			"areas" => array(),
			"outCounty" => "",
			"specialDesignFeatures" => "",
			"tenantPersonality" => "",
			"willingToShare" => "",
			"parkingFor" => "",

			"drugHistorial" => "",
			"drugHistorialDetails" => "",
			"sexualOffencesHistorial" => "",
			"sexualOffencesHistorialDetails" => "",
			"arsonHistorial" => "",
			"arsonHistorialDetails" => "",
			"evictionsHistorial" => "",
			"evictionsHistorialDetails" => "",
			"tenantReferences" => "",

			'contact_status' => "",
			"contact_method" => "",
			"contact_method_other" => "",
		));

	}

	public function testCgetAction()
	{

		$this->client->request($this->routes['api_get_tenants']['method'],
						 $this->routes['api_get_tenants']['uri']);

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
		$this->client->request($this->routes['api_post_tenant']['method'],
						 $this->routes['api_post_tenant']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
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
		$this->client->request($this->routes['api_post_tenant']['method'],
						 $this->routes['api_post_tenant']['uri'],
						 array('tenant' => array('name'=>'Pepe Luis')));

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
		$this->client->request($this->routes['api_post_tenant']['method'],
						 $this->routes['api_post_tenant']['uri'],
						 $this->newTenant);

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

		$GLOBALS['idtenant'] = $responseObj->data->id;
	}

	public function testPutAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_put_tenant']['method'],
						 sprintf($this->routes['api_put_tenant']['uri'], $GLOBALS['idtenant']),
						 $this->emptyTenant);
		/* TEST 1
		$this->client->request($this->routes['api_put_tenant']['method'],
						 sprintf($this->routes['api_put_tenant']['uri'], $GLOBALS['idtenant']),
						 $this->emptyTenant,
						 array(),
    					 array(
							'Content-Type'          => 'application/json',
							'X-Requested-With' => 'XMLHttpRequest',
							'Accept' => 'application/json, text/javascript, * ------ *; q=0.01'
    					));*/

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
		$this->emptyTenant['tenant']['name'] = "Pepe Luis";

		$this->client->request($this->routes['api_put_tenant']['method'],
						 sprintf($this->routes['api_put_tenant']['uri'], $GLOBALS['idtenant']),
						 $this->emptyTenant);

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
		$this->client->request($this->routes['api_put_tenant']['method'],
						 sprintf($this->routes['api_put_tenant']['uri'], $GLOBALS['idtenant']),
						 $this->editTenant);

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
		$this->client->request($this->routes['api_get_tenant']['method'],
						 sprintf($this->routes['api_get_tenant']['uri'], $GLOBALS['idtenant']));

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
		$this->client->request($this->routes['api_get_tenant']['method'],
						 sprintf($this->routes['api_get_tenant']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The tenant \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		// TEST 1
		$this->client->request($this->routes['api_delete_tenant']['method'],
						 $this->routes['api_delete_tenant']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idtenant is required", $responseObj->message);

		// TEST 2
		$this->client->request($this->routes['api_delete_tenant']['method'],
						 sprintf($this->routes['api_delete_tenant']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The tenant \"100\" doesn't exist.", $responseObj->message);

		// TEST 3
		$this->client->request($this->routes['api_delete_tenant']['method'],
						 sprintf($this->routes['api_delete_tenant']['uri'], $GLOBALS['idtenant']));

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