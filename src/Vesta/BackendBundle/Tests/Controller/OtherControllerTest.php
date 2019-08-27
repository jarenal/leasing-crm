<?php

namespace App\BackendBundle\Tests\Controller;

use App\ApiBundle\Utils\FakeNinGenerator;


class OtherControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php';

    public function setUp()
    {
        parent::setUp();

		//$window = new \WebDriverDimension(1280,1024);
		//$this->webDriver->manage()->window()->setSize($window);
    }

    /****************************************************************************************/
    /**** Basic Other Contacts **************************************************************/
    /****************************************************************************************/
    public function testBasicOther()
    {
		$faker = \Faker\Factory::create('en_GB');
		$faker->seed(10150); // Seed
		$contact = array();
		$contact['other_type'] = "Family member";
		$contact['name'] = $faker->firstName;
		$contact['surname'] = $faker->lastName;
		$contact['email'] = $faker->email;
		$contact['landline'] = $faker->phoneNumber;
		$contact['mobile'] = $faker->phoneNumber;
		$contact['address'] = $faker->streetAddress;
		$contact['postcode'] = $faker->postcode;
		$contact['town'] = $faker->city;
		$contact['job_title'] = "Supervisor";
		$contact['comments'] = $faker->text(500);

		$faker->seed(10160); // Seed
		$company = array();
		$company['name'] = $faker->company;
		$company['phone'] = $faker->phoneNumber;
		$company['email'] = $faker->email;
		$company['website'] = $faker->url;
		$company['address'] = $faker->streetAddress;
		$company['postcode'] = $faker->postcode;
		$company['town'] = $faker->city;

    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('loading-modal')
		  )
		);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-contact')
		  )
		);

		// Click in btn-add-contact
		$btnAddContact = $this->webDriver->findElement(\WebDriverBy::id('btn-add-contact'))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('loading-modal')
		  )
		);

		$this->webDriver->wait(100,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('choose_contact_type')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('choose_contact_type'))->findElement(\WebDriverBy::cssSelector("option[value='4']"))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-contact')
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
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('contact_title')
		  )
		);

		sleep(1);

		// Resto de campos
		$this->webDriver->findElement(\WebDriverBy::id('contact_other_type'))->findElement(\WebDriverBy::xpath("//option[contains(.,'{$contact['other_type']}')]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_title'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('contact_name'))->sendKeys($contact['name']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_surname'))->sendKeys($contact['surname']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_email'))->sendKeys($contact['email']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_landline'))->sendKeys($contact['landline']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_mobile'))->sendKeys($contact['mobile']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_address'))->clear()->sendKeys($contact['address']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_postcode'))->clear()->sendKeys($contact['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_town'))->clear()->sendKeys($contact['town']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_job_title'))->clear()->sendKeys($contact['job_title']);
		$this->webDriver->findElement(\WebDriverBy::id('contact_comments'))->clear()->sendKeys($contact['comments']);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contact'))->click();

		$this->webDriver->wait(10,500)->until(
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

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact')
		  )
		);

		/**************************/
        /*** 	Verify record     */
        /**************************/
        $this->assertContains($contact['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(2)"))->getText());
        $this->assertContains($contact['surname'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(3)"))->getText());

        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-show-contact'))->click();

        // Wait for back button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('div.pull-right:nth-child(1) > a:nth-child(1)')
		  )
		);

		$this->assertContains($contact['email'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#contact-details-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains($contact['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#address-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());
		$this->assertContains('N/A', $this->webDriver->findElement(\WebDriverBy::cssSelector("#administration-fieldset > div:nth-child(2) > div:nth-child(2)"))->getText());

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
		    \WebDriverBy::id('btn-save-contact')
		  )
		);

		$this->assertContains($company['name'], $this->webDriver->findElement(\WebDriverBy::id('combobox-organisation'))->getAttribute('value'));
		$this->assertContains($contact['job_title'], $this->webDriver->findElement(\WebDriverBy::id('contact_job_title'))->getAttribute('value'));
		$this->assertContains($contact['comments'], $this->webDriver->findElement(\WebDriverBy::id('contact_comments'))->getAttribute('value'));

		// Change status
		//$this->webDriver->findElement(\WebDriverBy::id('contact_status'))->findElement(\WebDriverBy::cssSelector("option[value='3']"))->click();

		//$this->waitForUserInput();

		// Save & verify
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-contact'))->click();

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

        sleep(1);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-contact')
		  )
		);

        // Verify new status
        $this->assertContains('N/A', $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:nth-child(5)"))->getText());

		$elementsBefore = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody > tr:last-child > td:last-child > div.btn-toolbar > a.btn-remove-contact"))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.text-center')
		  )
		);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-delete-contact')
		  )
		);

        // Verify name in popup delete
        $message = $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText();

        $this->assertContains("{$contact['name']} {$contact['surname']}", $message);

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

		$elementsAfter = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody > tr"));

		// Verify record doesn't exist in table.
		$this->assertCount(count($elementsBefore)-1, $elementsAfter);

		//$this->waitForUserInput();
    }
}