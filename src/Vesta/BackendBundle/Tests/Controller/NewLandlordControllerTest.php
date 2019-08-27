<?php

namespace App\BackendBundle\Tests\Controller;

use App\ApiBundle\Utils\FakeNinGenerator;

class NewLandlordControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php';
    protected $contact = array();
    protected $contact2 = array();

    public function setUp()
    {
    	parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(10015); // Seed
		$this->contact = array();

		/* LANDLORD 1*/
		// Type
		$this->contact['contact_type'] = array("id"=>1, "name"=>"Landlord/Investor");

		// Organisation
		$this->contact['contact_organisation'] = "Watson Ltd";

		// Basic details
		$this->contact['contact_title'] = array("id"=>7, "name"=>"Dr");
		$this->contact['contact_name'] = $faker->firstName;
		$this->contact['contact_surname'] = $faker->lastName;

		// Contact details
		$this->contact['contact_email'] = $faker->email;
		$this->contact['contact_landline'] = $faker->phoneNumber;
		$this->contact['contact_mobile'] = $faker->phoneNumber;
		$this->contact['contact_method'] = array("id"=>3, "name"=>"Post");
		$this->contact['contact_method_other'] = "";

		// Address
		$this->contact['contact_address'] = str_replace(array("\r", "\n"), '', $faker->streetAddress);
		$this->contact['contact_postcode'] = $faker->postcode;
		$this->contact['contact_town'] = $faker->city;

		// Invest
		$this->contact['contact_is_investor'] = 0;

		// Accreditation
		$this->contact['contact_accreditation'] = array("id"=>3, "name"=>"Yes - Other");
		$this->contact['contact_accreditation_references'] = $faker->text;

		// Miscellaneous
		$this->contact['contact_comments'] = str_replace(array("\r", "\n"), '', $faker->text(375));

		// Administration
		$this->contact['contact_status'] = array("id"=>2, "name"=>"Pending Approval");
		$this->contact['contact_created_by'] = "Administrator";
		$this->contact['contact_updated_by'] = "";

		/* LANDLORD 2*/
		$faker->seed(10025); // Seed

		// Type
		$this->contact2['contact_type'] = array("id"=>1, "name"=>"Landlord/Investor");

		// Organisation
		$this->contact2['contact_organisation'] = "Graham and Sons";

		// Basic details
		$this->contact2['contact_title'] = array("id"=>8, "name"=>"Lady");
		$this->contact2['contact_name'] = $faker->firstName;
		$this->contact2['contact_surname'] = $faker->lastName;

		// Contact details
		$this->contact2['contact_email'] = $faker->email;
		$this->contact2['contact_landline'] = $faker->phoneNumber;
		$this->contact2['contact_mobile'] = $faker->phoneNumber;
		$this->contact2['contact_method'] = array("id"=>4, "name"=>"Other");
		$this->contact2['contact_method_other'] = $faker->text(20);

		// Address
		$this->contact2['contact_address'] = str_replace(array("\r", "\n"), '', $faker->streetAddress);
		$this->contact2['contact_postcode'] = $faker->postcode;
		$this->contact2['contact_town'] = $faker->city;

		// Invest
		$this->contact2['contact_is_investor'] = 0;

		// Accreditation
		$this->contact2['contact_accreditation'] = array("id"=>1, "name"=>"Yes - Local Authority");
		$this->contact2['contact_accreditation_references'] = $faker->text(275);

		// Miscellaneous
		$this->contact2['contact_comments'] = str_replace(array("\r", "\n"), '', $faker->text(120));

		// Administration
		$this->contact2['contact_status'] = array("id"=>3, "name"=>"Approved");
		$this->contact2['contact_created_by'] = "Administrator";
		$this->contact2['contact_updated_by'] = "";

        //$this->waitForUserInput();
    }

    public function testCreateLandlord()
    {
    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		// Choose Landlord contact type
		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::xpath("//option[contains(.,'{$this->contact['contact_type']['name']}')]"))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-landlord')
		  )
		);

		// Fill data
		$this->fillData($this->contact);

		// Save contact
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

		// Verify alert
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The contact was created successfully', $message->getText());

    }

    public function testVerifyLandlordCreated()
    {
    	$this->loginAs('admin');
    	$this->verifyRecord($this->contact);
    }

    public function testModifyLandlord()
    {
    	$this->loginAs('user');
		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact'))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('fields-container')
		  )
		);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-landlord')
		  )
		);

		// Fill data
		$this->fillData($this->contact2);

		// Save contact
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertEquals('Congrats! The contact was updated successfully', $message->getText());
    }

    public function testVerifyLandlordModified()
    {
    	$this->loginAs('admin');
    	$this->verifyRecord($this->contact2);
    }

    public function testDeleteLandlord()
    {
    	$this->loginAs('admin');

		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// wait for add button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(3)')
		  )
		);

        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(3)"))->click();

        // Verify popup
        $this->assertContains("{$this->contact2['contact_name']} {$this->contact2['contact_surname']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-contact"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		sleep(1);

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(1)"));

		$this->assertCount(0, $elements);
    }

    private function verifyRecord($data)
    {
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact')
		  )
		);

		/***************************************/
        /*** 	Verify record in LIST view     */
        /***************************************/
        $this->assertEquals($data['contact_name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(2)"))->getText());
        $this->assertEquals($data['contact_surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(3)"))->getText());
        $this->assertEquals($data['contact_email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(4)"))->getText());
        $this->assertEquals($data['contact_status']['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(5)"))->getText());
        $this->assertEquals($data['contact_mobile'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(6)"))->getText());

        if(isset($data['contact_type']['name']) && $data['contact_type']['name'])
        {
			$this->assertEquals($data['contact_type']['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(7)"))->getText());
        }

    	//$this->waitForUserInput();

		/***************************************/
        /*** 	Verify record in SHOW view     */
        /***************************************/
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact'))->click();

        // Wait for back button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('a.btn')
		  )
		);

		// Type
        if(isset($data['contact_type']['name']) && $data['contact_type']['name'])
        {
			$this->assertEquals($data['contact_type']['name'], $this->webDriver->findElement(\WebDriverBy::id("contact_type"))->getText());
		}

		// Organisation
		$this->assertEquals($data['contact_organisation'], $this->webDriver->findElement(\WebDriverBy::id("contact_organisation"))->getText());

		// Basic details
		$this->assertEquals($data['contact_title']['name'], $this->webDriver->findElement(\WebDriverBy::id("contact_title"))->getText());
		$this->assertEquals($data['contact_name'], $this->webDriver->findElement(\WebDriverBy::id("contact_name"))->getText());
		$this->assertEquals($data['contact_surname'], $this->webDriver->findElement(\WebDriverBy::id("contact_surname"))->getText());

		// Contact details
		$this->assertEquals($data['contact_email'], $this->webDriver->findElement(\WebDriverBy::id("contact_email"))->getText());
		$this->assertEquals($data['contact_landline'], $this->webDriver->findElement(\WebDriverBy::id("contact_landline"))->getText());
		$this->assertEquals($data['contact_mobile'], $this->webDriver->findElement(\WebDriverBy::id("contact_mobile"))->getText());
		$this->assertEquals($data['contact_method']['name'], $this->webDriver->findElement(\WebDriverBy::id("contact_method"))->getText());
		//$this->waitForUserInput();
		if(isset($data['contact_method_other']) && $data['contact_method_other'])
			$this->assertEquals($data['contact_method_other'], $this->webDriver->findElement(\WebDriverBy::id("contact_method_other"))->getText());

		// Address
		$this->assertEquals($data['contact_address'], $this->webDriver->findElement(\WebDriverBy::id("contact_address"))->getText());
		$this->assertEquals($data['contact_postcode'], $this->webDriver->findElement(\WebDriverBy::id("contact_postcode"))->getText());
		$this->assertEquals($data['contact_town'], $this->webDriver->findElement(\WebDriverBy::id("contact_town"))->getText());

		// Invest
		$is_investor = $data['contact_is_investor']?"Yes":"No";
		$this->assertEquals($is_investor, $this->webDriver->findElement(\WebDriverBy::id("contact_is_investor"))->getText());

		// Accreditation
		$this->assertEquals($data['contact_accreditation']['name'], $this->webDriver->findElement(\WebDriverBy::id("contact_accreditation"))->getText());
		$this->assertEquals($data['contact_accreditation_references'], $this->webDriver->findElement(\WebDriverBy::id("contact_accreditation_references"))->getText());

		// Miscellaneous
		$this->assertEquals($data['contact_comments'], $this->webDriver->findElement(\WebDriverBy::id("contact_comments"))->getText());

		// Administration
		$this->assertEquals($data['contact_status']['name'], $this->webDriver->findElement(\WebDriverBy::id("contact_status"))->getText());

		$this->assertEquals($data['contact_created_by'], $this->webDriver->findElement(\WebDriverBy::id("contact_created_by"))->getText());
		if($data['contact_updated_by'])
			$this->assertEquals($data['contact_updated_by'], $this->webDriver->findElement(\WebDriverBy::id("contact_updated_by"))->getText());

		// go to index (back button)
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn'))->click();

		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact')
		  )
		);

		/***************************************/
        /*** 	Verify record in EDIT view     */
        /***************************************/
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-landlord')
		  )
		);

		// Type
		$this->assertEquals($data['contact_type']['id'], $this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->getAttribute('value'));

		// Organisation

		// Basic details
		$this->assertEquals($data['contact_title']['id'], $this->webDriver->findElement(\WebDriverBy::id('contact_title'))->getAttribute('value'));
		$this->assertEquals($data['contact_name'], $this->webDriver->findElement(\WebDriverBy::id('contact_name'))->getAttribute('value'));
		$this->assertEquals($data['contact_surname'], $this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->getAttribute('value'));

		// Contact details
		$this->assertEquals($data['contact_email'], $this->webDriver->findElement(\WebDriverBy::id('contact_email'))->getAttribute('value'));
		$this->assertEquals($data['contact_landline'], $this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->getAttribute('value'));
		$this->assertEquals($data['contact_mobile'], $this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->getAttribute('value'));
		$this->assertEquals($data['contact_method']['id'], $this->webDriver->findElement(\WebDriverBy::id('contact_method'))->getAttribute('value'));

		if(isset($data['contact_method_other']) && $data['contact_method_other'])
			$this->assertEquals($data['contact_method_other'], $this->webDriver->findElement(\WebDriverBy::id('contact_method_other'))->getAttribute('value'));

		// Address
		$this->assertEquals($data['contact_address'], $this->webDriver->findElement(\WebDriverBy::id('contact_address'))->getAttribute('value'));
		$this->assertEquals($data['contact_postcode'], $this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->getAttribute('value'));
		$this->assertEquals($data['contact_town'], $this->webDriver->findElement(\WebDriverBy::id('contact_town'))->getAttribute('value'));

		// Invest

		// Accreditation
		$this->assertEquals($data['contact_accreditation']['id'], $this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation'))->getAttribute('value'));
		$this->assertEquals($data['contact_accreditation_references'], $this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation_references'))->getAttribute('value'));

		// Miscellaneous
		$this->assertEquals($data['contact_comments'], $this->webDriver->findElement(\WebDriverBy::id('contact_comments'))->getAttribute('value'));

		// Administration
		$this->assertEquals($data['contact_status']['id'], $this->webDriver->findElement(\WebDriverBy::id('contact_status'))->getAttribute('value'));
		$this->assertEquals($data['contact_created_by'], $this->webDriver->findElement(\WebDriverBy::id("contact_created_by"))->getText());
		if(isset($data['contact_updated_by']) && $data['contact_updated_by'])
			$this->assertEquals($data['contact_updated_by'], $this->webDriver->findElement(\WebDriverBy::id("contact_updated_by"))->getText());
    }

    private function fillData($data)
    {
		// Choose organisation
		$organisationField = $this->webDriver->findElement(\WebDriverBy::className('custom-combobox-input'));
		$organisationField->clear()->sendKeys($data['contact_organisation']);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('ui-menu-item')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('.ui-autocomplete li:nth-of-type(1)'))->click();

		// Basic details
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['contact_title']['name']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->clear()->sendKeys($data['contact_name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->clear()->sendKeys($data['contact_surname']);

		// Contact details
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->clear()->sendKeys($data['contact_email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->clear()->sendKeys($data['contact_landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->clear()->sendKeys($data['contact_mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_method'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['contact_method']['name']}')]"))->click();

		if($data['contact_method']['id']==4 && isset($data['contact_method_other']) && $data['contact_method_other'])
		{
			$this->webDriver->wait(10,500)->until(
			  \WebDriverExpectedCondition::visibilityOfElementLocated(
			    \WebDriverBy::id('contact_method_other')
			  )
			);
			$this->webDriver->findElement(\WebDriverBy::id('contact_method_other'))->clear()->sendKeys($data['contact_method_other']);
		}

		// Address
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($data['contact_address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($data['contact_postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($data['contact_town']);

		// Invest
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='landlord[is_investor]' and @value={$data['contact_is_investor']}]"))->click();

		// Accreditation
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['contact_accreditation']['name']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation_references'))->clear()->sendKeys($data['contact_accreditation_references']);

		// Miscellaneous
		$this->webDriver->findElement(\WebDriverBy::id('contact_comments'))->clear()->sendKeys($data['contact_comments']);

		// Administration
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['contact_status']['name']}')]"))->click();

		//$this->waitForUserInput();
    }
}