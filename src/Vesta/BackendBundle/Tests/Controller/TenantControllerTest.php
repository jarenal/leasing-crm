<?php

namespace App\BackendBundle\Tests\Controller;

use App\ApiBundle\Utils\FakeNinGenerator;


class TenantControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php';
    protected $contact = array();
    protected $company = array();
    protected $child = array();

    public function setUp()
    {
        parent::setUp();

		$faker = \Faker\Factory::create('en_GB');
		$fakeNinGenerator = new FakeNinGenerator();

		$faker->seed(10120); // Seed
		$this->contact['name'] = $faker->firstName;
		$this->contact['surname'] = $faker->lastName;
		$this->contact['email'] = $faker->email;
		$this->contact['landline'] = $faker->phoneNumber;
		$this->contact['mobile'] = $faker->phoneNumber;
		$this->contact['address'] = $faker->streetAddress;
		$this->contact['postcode'] = $faker->postcode;
		$this->contact['town'] = $faker->city;
		$this->contact['birthdate'] = $faker->date($format = 'd/m/Y', $max = '15 years ago');
		$this->contact['gender'] = "Female";
		$this->contact['marital_status'] = "Married";
		$this->contact['nin'] = $fakeNinGenerator->generateNINO();

		$faker->seed(10130); // Seed
		$this->company['name'] = $faker->company;
		$this->company['phone'] = $faker->phoneNumber;
		$this->company['email'] = $faker->email;
		$this->company['website'] = $faker->url;
		$this->company['address'] = $faker->streetAddress;
		$this->company['postcode'] = $faker->postcode;
		$this->company['town'] = $faker->city;

		$faker->seed(10140); // Seed
		$this->child[] = array("name" => $faker->firstName." ".$faker->lastName, "birthdate" => $faker->dateTimeBetween("10 years ago", "now")->format("d/m/Y"), "guardianship" => 0);
		$this->child[] = array("name" => $faker->firstName." ".$faker->lastName, "birthdate" => $faker->dateTimeBetween("5 years ago", "now")->format("d/m/Y"), "guardianship" => 1);
    }

    /****************************************************************************************/
    /**** Create Basic Tenant ***************************************************************/
    /****************************************************************************************/
    public function testCreateBasicTenant()
    {
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

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-tenant')
		  )
		);

		// Datos del contacto
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($this->contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($this->contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($this->contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($this->contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($this->contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($this->contact['address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($this->contact['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($this->contact['town']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_birthdate'))->clear()->sendKeys($this->contact['birthdate']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);
		$this->webDriver->findElement(\WebDriverBy::id('contact_gender'))->findElement(\WebDriverBy::xpath("//option[contains(.,'{$this->contact['gender']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_marital_status'))->findElement(\WebDriverBy::xpath("//option[contains(.,'{$this->contact['marital_status']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_nin'))->clear()->sendKeys($this->contact['nin']);

		// Create local authority
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.form-group:nth-child(6) > div:nth-child(2) > div:nth-child(4) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		sleep(1);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($this->company['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($this->company['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($this->company['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($this->company['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($this->company['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($this->company['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($this->company['town']);

		// Save local authority
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('modal-message')
		  )
		);

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-start-edit-children')
			)
		);

		// Editar children
		$this->webDriver->findElement(\WebDriverBy::id('btn-start-edit-children'))->click();
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('a.btn-success:nth-child(2)')
		  )
		);

		// Child #1
		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(1) input.child-name'))->sendKeys($this->child[0]['name']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(1) input.child-birthdate'))->sendKeys($this->child[0]['birthdate']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('ui-datepicker-div')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='child[1][guardianship]' and @value={$this->child[0]['guardianship']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(1) a.btn-success'))->click();

		// Child #2
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-child'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(2) a.btn-success')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-tenant'))->getLocationOnScreenOnceScrolledIntoView();
		$this->webDriver->findElement(\WebDriverBy::id('child-3'))->getLocationOnScreenOnceScrolledIntoView();

		sleep(1); // wait for knockout
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(2) input.child-name'))->sendKeys($this->child[1]['name']);
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(2) input.child-birthdate'))->sendKeys($this->child[1]['birthdate']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('ui-datepicker-div')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='child[2][guardianship]' and @value={$this->child[1]['guardianship']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(2) a.btn-success'))->click();

		// Child #3
		$this->webDriver->findElement(\WebDriverBy::id('btn-new-child'))->click();
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(3) a.btn-success')
		  )
		);
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-tenant'))->getLocationOnScreenOnceScrolledIntoView();
		$this->webDriver->findElement(\WebDriverBy::id('child-4'))->getLocationOnScreenOnceScrolledIntoView();
		sleep(1); // wait for knockout

		// Stop edit children
		$this->webDriver->findElement(\WebDriverBy::id('btn-stop-edit-children'))->click();

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

		$message = $this->webDriver->findElement(\WebDriverBy::cssSelector('#modal-message > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)'));
		$this->assertContains('There are unsaved changes. Please, save all the children before continue.', $message->getText());

		// Close message
		$this->webDriver->findElement(\WebDriverBy::id('modal-box-btn-close'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('modal-message')
		  )
		);

		// Remove child #3
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#children-form-container fieldset:nth-child(3) a.btn-remove-child'))->click();

		$this->webDriver->findElement(\WebDriverBy::id('children-fieldset'))->getLocationOnScreenOnceScrolledIntoView();

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#btn-stop-edit-children'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('contact_status')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='4']"))->click();

		$this->webDriver->wait(10,500)->until(
			\WebDriverExpectedCondition::elementToBeClickable(
			\WebDriverBy::id('btn-save-tenant')
			)
		);

		// Save Tenant
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-tenant'))->click();

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

    /****************************************************************************************/
    /**** Update Basic Tenant ***************************************************************/
    /****************************************************************************************/
    public function testUpdateBasicTenant()
    {
    	$this->loginAs('user');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		sleep(1);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact')
		  )
		);

		/**************************/
        /*** 	Verify record     */
        /**************************/
        $this->assertContains($this->contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(2)"))->getText());
        $this->assertContains($this->contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(3)"))->getText());

        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact'))->click();

        // Wait for back button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		//$this->waitForUserInput();

		// Basic Details
		$this->assertContains($this->contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#basic-details-fieldset > div:nth-child(3) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#basic-details-fieldset > div:nth-child(4) > div:nth-child(2)"))->getText());

		// Contact Details
		$this->assertContains($this->contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['landline'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(3) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['mobile'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(4) > div:nth-child(2)"))->getText());

		// Address
		$this->assertContains($this->contact['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['postcode'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(3) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['town'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(4) > div:nth-child(2)"))->getText());

		// General Details
		$this->assertContains($this->contact['birthdate'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#general-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['gender'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#general-details-fieldset > div:nth-child(3) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['marital_status'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#general-details-fieldset > div:nth-child(4) > div:nth-child(2)"))->getText());
		$this->assertContains($this->contact['nin'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#general-details-fieldset > div:nth-child(5) > div:nth-child(2)"))->getText());
		$this->assertContains($this->company['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#general-details-fieldset > div:nth-child(6) > div:nth-child(2)"))->getText());

		// Children
		$i=1;
		foreach ($this->child as $kid) {
			$this->assertContains($kid['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("div.panel:nth-child($i) > div:nth-child(1) > div:nth-child(1) > div:nth-child(2)"))->getText());
			$this->assertContains($kid['birthdate'], $this->webDriver->findElement(\WebDriverBy::cssSelector("div.panel:nth-child($i) > div:nth-child(1) > div:nth-child(2) > div:nth-child(2)"))->getText());
			$this->assertContains($kid['guardianship']?"Yes":"No", $this->webDriver->findElement(\WebDriverBy::cssSelector("div.panel:nth-child($i) > div:nth-child(1) > div:nth-child(3) > div:nth-child(2)"))->getText());
			$i++;
		}

		// Administration
		$this->assertContains('Needs housing', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(3) > div:nth-child(2)"))->getText());

		// go to index (back button)
		$this->webDriver->findElement(\WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)'))->click();

		// wait for edit button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact')
		  )
		);

		// Verify edit view
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-edit-contact'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-tenant')
		  )
		);

		// Change status
		$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='5']"))->click();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-tenant'))->click();

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

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-contact')
		  )
		);

        // Verify new status
        $this->assertContains('Pending housing', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(5)"))->getText());

		$elementsBefore = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-contact"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('modal-message')
		  )
		);

		sleep(1);

        // Verify name in popup delete
        $message = $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText();

        $this->assertContains("{$this->contact['name']} {$this->contact['surname']}", $message);

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::id("btn-delete-contact"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('alert-messages')
		  )
		);

		sleep(1); // Wait popup is loaded

		$this->assertContains('Congrats! The contact was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elementsAfter = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

		// Verify record doesn't exist in table.
		$this->assertCount(count($elementsBefore)-1, $elementsAfter);
    }
}