<?php

namespace App\BackendBundle\Tests\Controller;


class OrganisationControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php/organisation/list';
    protected $organisation = array();
    protected $organisation2 = array();

    public function setUp()
    {
        parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(20300); // Seed
		$this->organisation['name'] = $faker->company;
		$this->organisation['type'] = array("id"=>2, "name"=>"Housing association");
		$this->organisation['phone'] = $faker->phoneNumber;
		$this->organisation['email'] = $faker->email;
		$this->organisation['website'] = $faker->url;
		$this->organisation['address'] = $faker->streetAddress;
		$this->organisation['postcode'] = $faker->postcode;
		$this->organisation['town'] = $faker->city;
		$this->organisation['comments'] = str_replace(array("\r", "\n"), '', $faker->text(250));
		$this->organisation['created_by'] = "Administrator";
		$this->organisation['updated_by'] = "";

		$faker->seed(20400); // Seed
		$this->organisation2['name'] = $faker->company;
		$this->organisation2['type'] = array("id"=>3, "name"=>"Support agency");
		$this->organisation2['phone'] = $faker->phoneNumber;
		$this->organisation2['email'] = $faker->email;
		$this->organisation2['website'] = $faker->url;
		$this->organisation2['address'] = $faker->streetAddress;
		$this->organisation2['postcode'] = $faker->postcode;
		$this->organisation2['town'] = $faker->city;
		$this->organisation2['comments'] = str_replace(array("\r", "\n"), '', $faker->text(500));
		$this->organisation2['created_by'] = "Administrator";
		$this->organisation2['updated_by'] = "Dummy user";

        //$this->waitForUserInput();
    }

    public function testCreateOrganisation()
    {
    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		// Click in btn-add-organisation
		$this->webDriver->findElement(\WebDriverBy::id('btn-add-organisation'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-organisation')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$this->organisation['type']['name']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->sendKeys($this->organisation['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->sendKeys($this->organisation['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->sendKeys($this->organisation['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->sendKeys($this->organisation['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->sendKeys($this->organisation['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->sendKeys($this->organisation['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->sendKeys($this->organisation['town']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_comments'))->sendKeys($this->organisation['comments']);

		//$this->waitForUserInput();

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-organisation'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertEquals('Congrats! The organisation was created successfully', $message->getText());

    }

    public function testVerifyOrganisationCreated()
    {
    	$this->loginAs('admin');
    	$this->verifyRecord($this->organisation);
    }

    public function testModifyOrganisation()
    {
    	$this->loginAs('user');
		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-organisation')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-organisation'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-organisation')
		  )
		);

		// Modify data
		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$this->organisation2['type']['name']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($this->organisation2['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($this->organisation2['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($this->organisation2['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($this->organisation2['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($this->organisation2['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($this->organisation2['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($this->organisation2['town']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_comments'))->clear()->sendKeys($this->organisation2['comments']);

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-organisation'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertEquals('Congrats! The organisation was updated successfully', $message->getText());
    }

    public function testVerifyOrganisationModified()
    {
    	$this->loginAs('admin');
    	$this->verifyRecord($this->organisation2);
    }

    public function testDeleteOrganisation()
    {
    	$this->loginAs('admin');

		// wait for add button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-organisation')
		  )
		);

		$elementsBefore = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-organisation"))->click();

		$this->webDriver->wait(20,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.text-center')
		  )
		);

		sleep(1);

        // Verify name in popup delete
        $this->assertContains("{$this->organisation2['name']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-organisation"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		$this->assertContains('Congrats! The organisation was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elementsAfter = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

		// Verify record doesn't exist in table.
		$this->assertCount(count($elementsBefore)-1, $elementsAfter);
    }

    private function verifyRecord($organisation)
    {
        // Wait for back button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-organisation')
		  )
		);

		/***************************************/
        /*** 	Verify record in LIST view     */
        /***************************************/
        $this->assertEquals($organisation['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(2)"))->getText());
        $this->assertEquals($organisation['type']['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(3)"))->getText());
        $this->assertEquals($organisation['phone'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(4)"))->getText());
        $this->assertEquals($organisation['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(5)"))->getText());

		/***************************************/
        /*** 	Verify record in SHOW view     */
        /***************************************/
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-organisation'))->click();

        // Wait for back button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('a.btn')
		  )
		);

		$this->assertEquals($organisation['type']['name'], $this->webDriver->findElement(\WebDriverBy::id("organisation_type"))->getText());
		$this->assertEquals($organisation['name'], $this->webDriver->findElement(\WebDriverBy::id("organisation_name"))->getText());
		$this->assertEquals($organisation['phone'], $this->webDriver->findElement(\WebDriverBy::id("organisation_phone"))->getText());
		$this->assertEquals($organisation['email'], $this->webDriver->findElement(\WebDriverBy::id("organisation_email"))->getText());
		$this->assertEquals($organisation['website'], $this->webDriver->findElement(\WebDriverBy::id("organisation_website"))->getText());
		$this->assertEquals($organisation['address'], $this->webDriver->findElement(\WebDriverBy::id("organisation_address"))->getText());
		$this->assertEquals($organisation['postcode'], $this->webDriver->findElement(\WebDriverBy::id("organisation_postcode"))->getText());
		$this->assertEquals($organisation['town'], $this->webDriver->findElement(\WebDriverBy::id("organisation_town"))->getText());
		$this->assertEquals($organisation['comments'], $this->webDriver->findElement(\WebDriverBy::id("organisation_comments"))->getText());
		$this->assertEquals($organisation['created_by'], $this->webDriver->findElement(\WebDriverBy::id("organisation_created_by"))->getText());
		if($organisation['updated_by'])
			$this->assertEquals($organisation['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("organisation_updated_by"))->getText());

		// go to index (back button)
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn'))->click();

		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-organisation')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-organisation')
		  )
		);

		/***************************************/
        /*** 	Verify record in EDIT view     */
        /***************************************/
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-organisation'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-organisation')
		  )
		);

		$this->assertEquals($organisation['type']['id'], $this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->getAttribute('value'));
		$this->assertEquals($organisation['name'], $this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->getAttribute('value'));
		$this->assertEquals($organisation['phone'], $this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->getAttribute('value'));
		$this->assertEquals($organisation['email'], $this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->getAttribute('value'));
		$this->assertEquals($organisation['website'], $this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->getAttribute('value'));
		$this->assertEquals($organisation['address'], $this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->getAttribute('value'));
		$this->assertEquals($organisation['postcode'], $this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->getAttribute('value'));
		$this->assertEquals($organisation['town'], $this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->getAttribute('value'));
		$this->assertEquals($organisation['comments'], $this->webDriver->findElement(\WebDriverBy::id('organisation_comments'))->getAttribute('value'));
		$this->assertEquals($organisation['created_by'], $this->webDriver->findElement(\WebDriverBy::id('organisation_created_by'))->getText('value'));
		if($organisation['updated_by'])
			$this->assertEquals($organisation['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("organisation_updated_by"))->getText());
    }
}