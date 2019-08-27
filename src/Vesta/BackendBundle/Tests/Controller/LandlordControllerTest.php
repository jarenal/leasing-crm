<?php

namespace App\BackendBundle\Tests\Controller;

use App\ApiBundle\Utils\FakeNinGenerator;

class LandlordControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php';

    public function setUp()
    {
    	parent::setUp();
    }

    /****************************************************************************************/
    /**** Landlord basic ********************************************************************/
    /****************************************************************************************/
    public function testLandlordBasic()
    {
		$faker = \Faker\Factory::create('en_GB');
		$faker->seed(10000); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;
		$contact['address'] = $faker->streetAddress;
		$contact['postcode'] = $faker->postcode;
		$contact['town'] = $faker->city;
		$contact['accreditation_references'] = $faker->text;

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-landlord')
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

		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($contact['address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($contact['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($contact['town']);
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation_references'))->sendKeys($contact['accreditation_references']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('Unapproved', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

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
		    \WebDriverBy::cssSelector('#btn-save-landlord')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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

        $this->assertContains('Congrats! The contact was updated successfully', $message->getText());

		sleep(1); // wait table load

        // Verify new status
        $this->assertContains('Pending Approval', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(5)"))->getText());

        // Verify delete
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

        // Verify popup
        $this->assertContains("{$contact['name']} {$contact['surname']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

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


    /****************************************************************************************/
    /**** Landlord creating new organisation ************************************************/
    /****************************************************************************************/
    public function testLandlordWithNewOrganisation()
    {
		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(10010); // Seed
		$company = array();
		$company['name'] = $faker->company;
		$company['phone'] = $faker->phoneNumber;
		$company['email'] = $faker->email;
		$company['website'] = $faker->url;
		$company['address'] = $faker->streetAddress;
		$company['postcode'] = $faker->postcode;
		$company['town'] = $faker->city;

		$faker->seed(10020); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;
		$contact['address'] = $faker->streetAddress;
		$contact['postcode'] = $faker->postcode;
		$contact['town'] = $faker->city;
		$contact['accreditation_references'] = $faker->text;

        //$this->webDriver->executeScript("try { $.fx.off = true; } catch(e) {}", array());

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-landlord')
		  )
		);

		/* OLD Create new organisation
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='organisation[mode]' and @value=2]"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		sleep(1);

		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($faker->company);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($faker->phoneNumber);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($faker->email);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($faker->url);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($faker->streetAddress);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($faker->postcode);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($faker->city);

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-organisation'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('ui-menu-item')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('.ui-autocomplete li:nth-of-type(1)'))->click();
		*/

		// Create organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		sleep(1);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($company['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($company['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($company['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($company['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($company['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($company['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($company['town']);

		// Save organisation
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('contact_title')
		  )
		);

		sleep(1);

		// Resto de campos
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation_references'))->sendKeys($contact['accreditation_references']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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
		    \WebDriverBy::cssSelector('#btn-save-landlord')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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

		sleep(1);

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(13) > td:nth-child(1)"));

		$this->assertCount(0, $elements);

    }


    /****************************************************************************************/
    /**** Landlord Investor *****************************************************************/
    /****************************************************************************************/
    public function testInvestorBasic()
    {
		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(10030); // Seed
		$contact = array();
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;
		$contact['address'] = $faker->streetAddress;
		$contact['postcode'] = $faker->postcode;
		$contact['town'] = $faker->city;
		$contact['accreditation_references'] = $faker->text;

		$faker->seed(10040); // Seed
		$investments = array();
		$investments[] = array('postcode'=>$faker->postcode);
		$investments[] = array('postcode'=>$faker->postcode);

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		// Elegimos Landlord
		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-landlord')
		  )
		);

		// Elegimos organizacion
		$organisationField = $this->webDriver->findElement(\WebDriverBy::className('custom-combobox-input'));
		$organisationField->sendKeys("a");

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('ui-menu-item')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('.ui-autocomplete li:nth-of-type(1)'))->click();

		// Rellenamos formulario
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($contact['address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($contact['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($contact['town']);
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('landlord_accreditation_references'))->sendKeys($contact['accreditation_references']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='1']"))->click();

		// Si es investor
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='landlord[is_investor]' and @value=1]"))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-stop-edit-investments')
		  )
		);

		// New investment #1
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-1 input.investment-amount'))->sendKeys(rand(1,100));
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-1 > div:nth-child(2) > div:nth-child(2) > div:nth-child(1) > input:nth-child(2)'))->sendKeys(rand(1,100));
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-1 > div:nth-child(3) > div:nth-child(2) > select:nth-child(1) > option:nth-child(2)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-1 > div:nth-child(3) > div:nth-child(3) > input:nth-child(1)'))->sendKeys($investments[0]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-1 > div:nth-child(4) > div:nth-child(1) > a:nth-child(2) > span:nth-child(1)'))->click();
		// New investment #2
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-new-investment'))->click();
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-2 input.investment-amount'))->sendKeys(rand(1,100));
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-2 > div:nth-child(2) > div:nth-child(2) > div:nth-child(1) > input:nth-child(2)'))->sendKeys(rand(1,100));
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-2 > div:nth-child(3) > div:nth-child(2) > select:nth-child(1) > option:nth-child(2)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-2 > div:nth-child(3) > div:nth-child(3) > input:nth-child(1)'))->sendKeys($investments[1]['postcode']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-2 > div:nth-child(4) > div:nth-child(1) > a:nth-child(2) > span:nth-child(1)'))->click();

		// New investment #3
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-new-investment'))->click();
		sleep(1); // wait for knockout
		$this->stopEditInvestments();

		$message = $this->webDriver->findElement(\WebDriverBy::cssSelector('#modal-message > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)'));
		$this->assertContains('There are unsaved changes. Please, save all the investments before continue.', $message->getText());
		$this->webDriver->findElement(\WebDriverBy::id('modal-box-btn-close'))->click();
		sleep(1); // wait for close popup
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#investment-3 > div:nth-child(4) > div:nth-child(1) > a:nth-child(1) > span:nth-child(1)'))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-investments'))->click();

		// Guardamos formulario
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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

		$investElements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#investments-form-container div.panel"));
		$this->assertCount(2, $investElements);

		$this->assertContains($investments[0]['postcode'], $investElements[0]->findElement(\WebDriverBy::cssSelector("div:nth-child(1) > div:nth-child(3) > div:nth-child(2)"))->getText());
		$this->assertContains($investments[1]['postcode'], $investElements[1]->findElement(\WebDriverBy::cssSelector("div:nth-child(1) > div:nth-child(3) > div:nth-child(2)"))->getText());

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
		    \WebDriverBy::cssSelector('#btn-save-landlord')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-landlord'))->click();

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

    private function stopEditInvestments()
    {
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-investments'))->click();

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