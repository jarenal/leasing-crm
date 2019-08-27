<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;
$idorganisation=null;

class OrganisationControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newOrganisation;
	protected $editOrganisation;
	protected $emptyOrganisation;

	protected function setUp()
	{
		parent::setUp();

		$this->fields = array('"message":"validation"',
								'name',
								'phone',
								'address',
								'postcode',
								'town',
								'organisation_type');

		$this->routes = array("api_get_organisations"   => array("method" => "GET",   "uri" => "/api/organisations"),
							  "api_get_organisations_combobox"   => array("method" => "GET",    "uri" => "/api/organisations/combobox"),
							  "api_post_organisation"   => array("method" => "POST",   "uri" => "/api/organisations"),
							  "api_put_organisation"    => array("method" => "PUT",    "uri" => "/api/organisations/%s"),
							  "api_delete_organisation" => array("method" => "DELETE", "uri" => "/api/organisations/%s"),
							  );

		$this->newOrganisation = array("organisation"=>array("name" => "Banco general",
			"phone" => 123456789,
			"email" => "banco.general@example.com",
			"website" => "http://www.example.com",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"organisation_type" => 1
		));

		$this->editOrganisation = array("organisation"=>array("name" => "Banco general 2",
			"phone" => 987654321,
			"email" => "banco2.general@example.com",
			"website" => "http://www.example2.com",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"organisation_type" => 1
		));

		$this->emptyOrganisation = array("organisation"=>array("name" => "",
			"phone" => "",
			"email" => "",
			"website" => "",
			"address" => "",
			"postcode" => "",
			"town" => "",
			"organisation_type" => ""
		));
	}

	public function testCgetAction()
	{
		$this->client->request($this->routes['api_get_organisations']['method'],
						 $this->routes['api_get_organisations']['uri']);

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

		$this->client->request($this->routes['api_get_organisations_combobox']['method'],
						 $this->routes['api_get_organisations_combobox']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

	public function testPostAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_post_organisation']['method'],
						 $this->routes['api_post_organisation']['uri']);

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
		$this->client->request($this->routes['api_post_organisation']['method'],
						 $this->routes['api_post_organisation']['uri'],
						 array('organisation' => array('name'=>'Company General')));

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
		$this->client->request($this->routes['api_post_organisation']['method'],
						 $this->routes['api_post_organisation']['uri'],
						 $this->newOrganisation);

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

		$GLOBALS['idorganisation'] = $responseObj->data->id;
	}

	public function testPutAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_put_organisation']['method'],
						 sprintf($this->routes['api_put_organisation']['uri'], $GLOBALS['idorganisation']),
						 $this->emptyOrganisation);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type','application/json'),
			"Content-type doesn't match."
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful(), "HTTP request is not ok.");

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
		$this->emptyOrganisation['organisation']['name'] = "Dummy company S.A.";

		$this->client->request($this->routes['api_put_organisation']['method'],
						 sprintf($this->routes['api_put_organisation']['uri'], $GLOBALS['idorganisation']),
						 $this->emptyOrganisation);

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
		$this->client->request($this->routes['api_put_organisation']['method'],
						 sprintf($this->routes['api_put_organisation']['uri'], $GLOBALS['idorganisation']),
						 $this->editOrganisation);

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

	public function testDeleteAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_delete_organisation']['method'],
						 $this->routes['api_delete_organisation']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idorganisation is required", $responseObj->message);

		/* TEST 2 */
		$this->client->request($this->routes['api_delete_organisation']['method'],
						 sprintf($this->routes['api_delete_organisation']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The organisation \"100\" doesn't exist.", $responseObj->message);

		/* TEST 3 */
		$this->client->request($this->routes['api_delete_organisation']['method'],
						 sprintf($this->routes['api_delete_organisation']['uri'], $GLOBALS['idorganisation']));

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