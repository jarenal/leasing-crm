<?php

namespace App\BackendBundle\Tests\Controller;

use App\ApiBundle\Utils\FakeNinGenerator;

class ContractorControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php/contact';

    public function setUp()
    {
    	parent::setUp();
    }

    /****************************************************************************************/
    /**** Basic contractor ******************************************************************/
    /****************************************************************************************/
    public function testContractorBasic()
    {
		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(10050); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;
		$contact['address'] = $faker->streetAddress;
		$contact['postcode'] = $faker->postcode;
		$contact['town'] = $faker->city;

		$faker->seed(10051); // Seed
		$areas = array();
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);

		$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(100)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='3']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		$organisationField = $this->webDriver->findElement(\WebDriverBy::className('custom-combobox-input'));
		$organisationField->sendKeys("a");

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('ui-menu-item')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('.ui-autocomplete li:nth-of-type(1)'))->click();

		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($contact['address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($contact['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($contact['town']);

		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'General')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Electrician')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Decorator')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[require_certification]' and @value=0]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[liability_insurance]' and @value=1]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		// Editar Areas
		$this->webDriver->findElement(\WebDriverBy::id('btn-start-edit-areas'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('a.btn-success:nth-child(2)')
		  )
		);

		// Area #1
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-distance > option:nth-child(2)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-postcode'))->sendKeys($areas[0]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-1 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #2
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[1]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #3
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[2]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #4
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout

		// Stop edit areas
		$this->stopEditAreas();

		$message = $this->webDriver->findElement(\WebDriverBy::cssSelector('#modal-message > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)'));
		$this->assertContains('There are unsaved changes. Please, save all the areas before continue.', $message->getText());
		$this->webDriver->findElement(\WebDriverBy::id('modal-box-btn-close'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
			)
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)'))->click();
		//$this->waitForUserInput();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-areas'))->click();

		$this->webDriver->wait(10)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-save-contractor')
			)
		);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

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

        sleep(1); // reload table

		/**************************/
        /*** 	Verify record     */
        /**************************/
        $this->assertContains($contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(2)"))->getText());
        $this->assertContains($contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(3)"))->getText());


        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('Unapproved', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

		$this->assertContains('General', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Decorator', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Electrician', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());

		// go to index
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		// Verify edit view
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The contact was updated successfully', $message->getText());

        // Verify new status
        $this->assertContains('Pending Approval', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(5)"))->getText());

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(3)"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-delete-contact')
		  )
		);

        // Verify name in popup delete
        $this->assertContains("{$contact['name']} {$contact['surname']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-contact"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		sleep(1); // Wait popup is loaded

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(1)"));

		// Verify record doesn't exist in table.
		$this->assertCount(0, $elements);

    }

    /****************************************************************************************/
    /**** Contractor with new organisation **************************************************/
    /****************************************************************************************/
    public function testContractorWithNewOrganisation()
    {
		$faker = \Faker\Factory::create('en_GB');
		$faker->seed(10060); // Seed
		$company = array();
		$company['name'] = $faker->company;
		$company['phone'] = $faker->phoneNumber;
		$company['email'] = $faker->email;
		$company['website'] = $faker->url;
		$company['address'] = $faker->streetAddress;
		$company['postcode'] = $faker->postcode;
		$company['town'] = $faker->city;

		$faker->seed(10070); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;

		$faker->seed(10071); // Seed
		$areas = array();
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(100)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='3']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		// Create organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		sleep(1);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($company['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($company['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($company['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($company['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($company['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($company['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($company['town']);

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn-success'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('contact_title')
		  )
		);

		sleep(1);

		// Resto de campos
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);

		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'General')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Electrician')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Decorator')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[require_certification]' and @value=0]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[liability_insurance]' and @value=1]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		// Editar Areas
		$this->webDriver->findElement(\WebDriverBy::id('btn-start-edit-areas'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('a.btn-success:nth-child(2)')
		  )
		);

		// Area #1
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-distance > option:nth-child(2)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-postcode'))->sendKeys($areas[0]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-1 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #2
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[1]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #3
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[2]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #4
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout

		// Stop edit areas
		$this->stopEditAreas();

		$message = $this->webDriver->findElement(\WebDriverBy::cssSelector('#modal-message > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)'));
		$this->assertContains('There are unsaved changes. Please, save all the areas before continue.', $message->getText());
		$this->webDriver->findElement(\WebDriverBy::id('modal-box-btn-close'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
			)
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-areas'))->click();

		$this->webDriver->wait(10)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-save-contractor')
			)
		);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

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

        sleep(1); // reload table

		/**************************/
        /*** 	Verify record     */
        /**************************/
        $this->assertContains($contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(2)"))->getText());
        $this->assertContains($contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(3)"))->getText());


        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($company['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('Unapproved', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

		$this->assertContains('General', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Decorator', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Electrician', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());

		// go to index
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		// Verify edit view
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The contact was updated successfully', $message->getText());

        // Verify new status
        $this->assertContains('Pending Approval', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(5)"))->getText());

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(3)"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-delete-contact')
		  )
		);

        // Verify name in popup delete
        $this->assertContains("{$contact['name']} {$contact['surname']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-contact"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		sleep(1); // Wait popup is loaded

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(1)"));

		// Verify record doesn't exist in table.
		$this->assertCount(0, $elements);

    }

    /****************************************************************************************/
    /**** Contractor with Areas *************************************************************/
    /****************************************************************************************/
    public function testContractorWithAreas()
    {
		$faker = \Faker\Factory::create('en_GB');
		$faker->seed(10080); // Seed
		$company = array();
		$company['name'] = $faker->company;
		$company['phone'] = $faker->phoneNumber;
		$company['email'] = $faker->email;
		$company['website'] = $faker->url;
		$company['address'] = $faker->streetAddress;
		$company['postcode'] = $faker->postcode;
		$company['town'] = $faker->city;

		$faker->seed(10090); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;

		$faker->seed(10100); // Seed
		$contact2 = array();
		$contact2['name'] = $faker->firstName;
		$contact2['surname'] = $faker->lastName;
		$contact2['email'] = $faker->email;
		$contact2['landline'] = $faker->phoneNumber;
		$contact2['mobile'] = $faker->phoneNumber;

		$faker->seed(10110); // Seed
		$areas = array();
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);
		$areas[] = array('postcode' => $faker->postcode);

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(100)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='3']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		// Create organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		sleep(1);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($company['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($company['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($company['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($company['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($company['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($company['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($company['town']);

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn-success'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('contact_title')
		  )
		);

		sleep(1);

		// Resto de campos
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);

		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'General')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Plumber')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Roofer')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Decorator')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[require_certification]' and @value=0]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='contact[liability_insurance]' and @value=1]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		// Editar Areas
		$this->webDriver->findElement(\WebDriverBy::id('btn-start-edit-areas'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('a.btn-success:nth-child(2)')
		  )
		);

		// Area #1
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-distance > option:nth-child(2)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('.area-postcode'))->sendKeys($areas[0]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-1 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #2
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[1]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #3
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(3)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[2]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Area #4
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);
		sleep(1); // wait for knockout

		// Stop edit areas
		$this->stopEditAreas();

		$message = $this->webDriver->findElement(\WebDriverBy::cssSelector('#modal-message > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)'));
		$this->assertContains('There are unsaved changes. Please, save all the areas before continue.', $message->getText());
		$this->webDriver->findElement(\WebDriverBy::id('modal-box-btn-close'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
			)
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-areas'))->click();

		$this->webDriver->wait(10)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-save-contractor')
			)
		);

		// Save contractor
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('loading-modal')
		  )
		);

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

        sleep(1); // reload table

		/*******************************/
        /*** 	Verify record step #1  */
        /*******************************/
        $this->assertContains($contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(2)"))->getText());
        $this->assertContains($contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(3)"))->getText());


        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($company['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('Unapproved', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

		$this->assertContains('General', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Decorator', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Plumber', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Roofer', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());

		// Assert we have two areas
		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#areas-form-container div.panel.panel-default"));
		$this->assertCount(3, $elements);

		// go to index
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		// to edit contractor
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('loading-modal')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-contractor')
		  )
		);

		/************************/
		/* Change basic details */
		/************************/
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->clear();
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->clear();
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->clear();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact2['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact2['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact2['email']);

		/******************/
		/* Remove Plumber */
		/******************/
		$action = new \WebDriverActions($this->webDriver);
		$element = $this->webDriver->findElement(\WebDriverBy::id("works-fieldset"));
		$action->moveToElement($element)->perform();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::xpath("//label[contains(text(),'Plumber')]")
		  )
		);
		$this->webDriver->findElement(\WebDriverBy::xpath("//label[contains(text(),'Plumber')]"))->click();

		/****************/
		/* Change areas */
		/****************/
		$this->webDriver->findElement(\WebDriverBy::id('btn-start-edit-areas'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('a.btn-success:nth-child(2)')
		  )
		);

		// New Area #4
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-area'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(1) > div:nth-child(4) > select:nth-child(1) > option:nth-child(1)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(1) > div:nth-child(5) > input:nth-child(1)'))->sendKeys($areas[3]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-4 > div:nth-child(2) > div:nth-child(1) > a:nth-child(2)'))->click();

		// Remove Area #3
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(3)'))->click(); // edit
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-3 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)'))->click(); // remove
		sleep(1);
		// Remove Area #2
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(3)'))->click(); //edit
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#area-2 > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)'))->click(); // remove
		sleep(1);

		// stop edit areas
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-areas'))->click();

		$this->webDriver->wait(10)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-save-contractor')
			)
		);

		/********************/
		/* END Change areas */
		/********************/

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contractor'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The contact was updated successfully', $message->getText());

		/*********************************/
        /*** 	Verify record step #2    */
        /*********************************/
        // Verify new status
        $this->assertContains('Pending Approval', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(5)"))->getText());

        // Verify name
        $this->assertContains($contact2['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(2)"))->getText());
        $this->assertContains($contact2['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(3)"))->getText());

        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact2['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($company['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('Pending Approval', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

		$this->assertContains('General', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Decorator', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());

		// Verify Plumber doesn't exist now
		$this->assertNotContains('Plumber', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());
		$this->assertContains('Roofer', $this->webDriver->findElement(\WebDriverBy::cssSelector(".form-horizontal > fieldset:nth-child(6) > div:nth-child(2)"))->getText());

		// Assert we have two areas
		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#areas-form-container div.panel.panel-default"));
		$this->assertCount(2, $elements);

		// go to index
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(8) > div:nth-child(1) > a:nth-child(3)"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-delete-contact')
			)
		);
        // Verify name in popup delete
        $this->assertContains("{$contact2['name']} {$contact2['surname']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-contact"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		sleep(1); // Wait popup is loaded

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(1)"));

		// Verify record doesn't exist in table.
		$this->assertCount(0, $elements);

    }

    private function stopEditAreas()
    {
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-areas'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('modal-box-btn-close')
			)
		);
    }
}