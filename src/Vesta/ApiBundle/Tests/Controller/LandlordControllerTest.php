<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

$idlandlord = null;

class LandlordControllerTest extends ApiWebTestCase
{
	protected $fields;
	protected $routes;
	protected $newLandlord;
	protected $editLandlord;
	protected $emptyLandlord;

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
								'contact_title',
								'landlord_accreditation',
								'status');

		$this->routes = array("api_get_landlord"    => array("method" => "GET",    "uri" => "/api/landlords/%s"),
							  "api_get_landlords"   => array("method" => "GET",    "uri" => "/api/landlords"),
							  "api_get_landlords_combobox"   => array("method" => "GET",    "uri" => "/api/landlords/combobox"),
							  "api_post_landlord"   => array("method" => "POST",   "uri" => "/api/landlords"),
							  "api_put_landlord"    => array("method" => "PUT",    "uri" => "/api/landlords/%s"),
							  "api_delete_landlord" => array("method" => "DELETE", "uri" => "/api/landlords/%s"),
							  );

		$this->newLandlord = array("landlord"=>array("contact_type" => 1,
			"contact_title" => 1,
			"organisation" => 1,
			"name" => "Landlord",
			"surname" => "Garcia",
			"email" => "landlord.garcia@example.com",
			"landline" => "01625536709",
			"mobile" => "07957574495",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"accreditation_references" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis consequat augue quis eros dictum scelerisque. Morbi at odio lacus. Nam nec porta tortor, at rutrum justo. Suspendisse eget dolor sit amet dolor fringilla suscipit at in mi. Ut facilisis molestie erat at rhoncus. Cras sit amet magna id justo blandit dignissim. Cras ut metus tempus, pellentesque libero tempor, placerat lorem. Vivamus gravida, ex fringilla consectetur consequat, nisi enim ornare magna, in faucibus ligula quam a quam. Aliquam tempor imperdiet nunc vitae pulvinar. Nulla tellus ante, consequat ac est quis, lobortis viverra dui.",
			"contact_status" => 1,
			"landlord_accreditation" => 3,
			"investments" => array(
									array("amount" => 12345.50,
										"desired_return" => 99999.25,
										"distance" => 5,
										"postcode" => "W1S",
									),
									array("amount" => 88888.30,
										"desired_return" => 12345.99,
										"distance" => 10,
										"postcode" => "W1S",
									)
			),
			"contact_method" => 1,
			"contact_method_other" => "",
		));

		$this->editLandlord = array("landlord"=>array("contact_type" => 1,
			"contact_title" => 1,
			"organisation" => 1,
			"name" => "Landlord Edit",
			"surname" => "Garcia Edit",
			"email" => "landlord.garcia.edit@example.com",
			"landline" => "01625536709",
			"mobile" => "07957574495",
			"address" => "25 Old Bond ST",
			"postcode" => "W1S",
			"town" => "London",
			"accreditation_references" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis consequat augue quis eros dictum scelerisque. Morbi at odio lacus. Nam nec porta tortor, at rutrum justo. Suspendisse eget dolor sit amet dolor fringilla suscipit at in mi. Ut facilisis molestie erat at rhoncus. Cras sit amet magna id justo blandit dignissim. Cras ut metus tempus, pellentesque libero tempor, placerat lorem. Vivamus gravida, ex fringilla consectetur consequat, nisi enim ornare magna, in faucibus ligula quam a quam. Aliquam tempor imperdiet nunc vitae pulvinar. Nulla tellus ante, consequat ac est quis, lobortis viverra dui.",
			"contact_status" => 1,
			"landlord_accreditation" => 3,
			"investments" => array(
									array("id"=>1,
										"amount" => 25555.60,
										"desired_return" => 3000.78,
										"distance" => 3,
										"postcode" => "W1S",
									),
									array("amount" => 1500.22,
										"desired_return" => 4000.25,
										"distance" => 40,
										"postcode" => "W1S",
									)
			),
			"contact_method" => 4,
			"contact_method_other" => "Smoke signals",
		));

		$this->emptyLandlord = array("landlord"=>array("contact_type" => "",
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
			"accreditation_references" => "",
			"contact_status" => "",
			"landlord_accreditation" => "",
			"contact_method" => "",
			"contact_method_other" => "",
		));
	}

	public function testCgetAction()
	{
		$this->client->request($this->routes['api_get_landlords']['method'],
						 $this->routes['api_get_landlords']['uri']);

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
		$this->client->request($this->routes['api_get_landlords_combobox']['method'],
						 $this->routes['api_get_landlords_combobox']['uri']);

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
		$this->client->request($this->routes['api_post_landlord']['method'],
						 $this->routes['api_post_landlord']['uri']);

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
		$this->client->request($this->routes['api_post_landlord']['method'],
						 $this->routes['api_post_landlord']['uri'],
						 array('landlord' => array('name'=>'Pepe Luis')));

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
		$this->client->request($this->routes['api_post_landlord']['method'],
						 $this->routes['api_post_landlord']['uri'],
						 $this->newLandlord);

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

		$GLOBALS['idlandlord'] = $responseObj->data->id;
	}

	public function testPutAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_put_landlord']['method'],
						 sprintf($this->routes['api_put_landlord']['uri'], $GLOBALS['idlandlord']),
						 $this->emptyLandlord);

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
		$this->emptyLandlord['landlord']['name'] = "Pepe Luis";

		$this->client->request($this->routes['api_put_landlord']['method'],
						 sprintf($this->routes['api_put_landlord']['uri'], $GLOBALS['idlandlord']),
						 $this->emptyLandlord);

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
		$this->client->request($this->routes['api_put_landlord']['method'],
						 sprintf($this->routes['api_put_landlord']['uri'], $GLOBALS['idlandlord']),
						 $this->editLandlord);

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
		$this->client->request($this->routes['api_get_landlord']['method'],
						 sprintf($this->routes['api_get_landlord']['uri'], $GLOBALS['idlandlord']));

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
		$this->client->request($this->routes['api_get_landlord']['method'],
						 sprintf($this->routes['api_get_landlord']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The landlord \"100\" doesn't exist.", $responseObj->message);
	}

	public function testDeleteAction()
	{
		/* TEST 1 */
		$this->client->request($this->routes['api_delete_landlord']['method'],
						 $this->routes['api_delete_landlord']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The idlandlord is required", $responseObj->message);

		/* TEST 2 */
		$this->client->request($this->routes['api_delete_landlord']['method'],
						 sprintf($this->routes['api_delete_landlord']['uri'], 100));

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// convert response to an object
		$responseObj = json_decode($this->client->getResponse()->getContent());
		$this->assertEquals("The landlord \"100\" doesn't exist.", $responseObj->message);

		/* TEST 3 */
		$this->client->request($this->routes['api_delete_landlord']['method'],
						 sprintf($this->routes['api_delete_landlord']['uri'], $GLOBALS['idlandlord']));

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