<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Tests\Controller\ApiWebTestCase;

class ContactControllerTest extends ApiWebTestCase
{
	protected $routes;

	protected function setUp()
	{
		parent::setUp();
		$this->routes = array("api_get_contacts"   => array("method" => "GET",    "uri" => "/api/contacts"),
			"api_get_contacts_titles"   => array("method" => "GET",    "uri" => "/api/contacts/titles"),
			"api_get_contacts_statuses"   => array("method" => "GET",    "uri" => "/api/contacts/statuses"),
			"api_get_contacts_combobox"   => array("method" => "GET",    "uri" => "/api/contacts/combobox"));
	}

	public function testCgetAction()
	{
		$this->client->request($this->routes['api_get_contacts']['method'],
						 $this->routes['api_get_contacts']['uri']);

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

	public function testCgetTitlesAction()
	{
		$this->client->request($this->routes['api_get_contacts_titles']['method'],
						 $this->routes['api_get_contacts_titles']['uri']);

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

		// Verify response
		$this->assertJsonStringEqualsJsonString(
			'{"error":0,"data":[{"id":1,"name":"Mr"},{"id":2,"name":"Miss"},{"id":3,"name":"Mrs"},{"id":4,"name":"Ms"},{"id":5,"name":"Canon"},{"id":6,"name":"Cllr"},{"id":7,"name":"Dr"},{"id":8,"name":"Lady"},{"id":9,"name":"Prof"},{"id":10,"name":"Rev"},{"id":11,"name":"Sir"},{"id":12,"name":"Sister"}],"message":"success"}',
			$this->client->getResponse()->getContent()
		);

	}

	public function testCgetStatusesAction()
	{
		$this->client->request($this->routes['api_get_contacts_statuses']['method'],
						 $this->routes['api_get_contacts_statuses']['uri']);

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

		// Verify response
		$this->assertJsonStringEqualsJsonString(
			'{"error":0,"data":[{"id":1,"name":"Unapproved"},{"id":2,"name":"Pending Approval"},{"id":3,"name":"Approved"},{"id":4,"name":"Needs housing"},{"id":5,"name":"Pending housing"},{"id":6,"name":"Housed"},{"id":7,"name":"Ineligible"},{"id":8,"name":"N\/A"}],"message":"success"}',
			$this->client->getResponse()->getContent()
		);

	}

	public function testCgetComboboxAction()
	{
		$this->client->request($this->routes['api_get_contacts_combobox']['method'],
						 $this->routes['api_get_contacts_combobox']['uri']);

		// Content-type is json?
		$this->assertTrue(
			$this->client->getResponse()->headers->contains('Content-type',
				'application/json')
		);

		// The http request is ok?
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}
}