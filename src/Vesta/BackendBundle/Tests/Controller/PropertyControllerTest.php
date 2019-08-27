<?php

namespace App\BackendBundle\Tests\Controller;

class PropertyControllerTest extends AppBaseTestCase
{
    protected $url = 'http://localhost/app_test.php/property/list';
    protected $property = array();

    public function setUp()
    {
    	parent::setUp();

		$faker = \Faker\Factory::create('en_GB');

		$faker->seed(20000); // Seed
		$this->property['address'] = $faker->streetAddress;
		$this->property['postcode'] = $faker->postcode;
		$this->property['town'] = $faker->city;
		$this->property['available_date'] = date("d/m/Y", strtotime("next year"));
		$this->property['requirements_on'] = array();
		$this->property['requirements_on']['property_type'] = array(1=>"Detached",
																	2=>"Terraced",
																	3=>"Flats/Apartments",
																	4=>"Semi-detached",
																	5=>"Bungalow");
		$this->property['requirements_on']['bedrooms'] = array(6=>"1 bedroom",
																7=>"2 bedrooms",
																8=>"3 bedrooms",
																9=>"4 bedrooms",
																10=>"5 bedrooms",
																11=>"6 bedrooms");
		$this->property['requirements_on']['floors'] = array(36=>"1 floor",
															37=>"2 floors",
															38=>"3 floors",
															39=>"4 or more floors");
		$this->property['requirements_on']['furnitures'] = array(12=>"Furnished",
																13=>"Unfurnished",
																14=>"Part furnished");
		$this->property['requirements_on']['garden_details'] = array(20=>"Front garden",
															21=>"Fenced and secure",
															22=>"Large back garden",
															23=>"Back garden",
															24=>"Private/not overlooked",
															25=>"Communal garden");
		$this->property['requirements_off'] = array();
		$this->property['previous_crimes_description'] = $faker->text(250);
		$this->property['special_design_features'] = $faker->text(250);
		$this->property['value'] = $faker->numberBetween(150000,500000);
		$this->property['valuation_date'] = $faker->dateTimeBetween("5 years ago", "now")->format("d/m/Y");
		$this->property['target_rent'] = $faker->numberBetween(350,600);

		$this->property['accessible'] = 26;
		$this->property['willing_to_adapt'] = 33;
		$this->property['smoking'] = 29;
		$this->property['pets'] = 31;
		$this->property['previous_crimes_near'] = 1;

		$this->property['mortgage_outstanding'] = 1;
		$this->property['buy_to_let'] = 1;
		$this->property['land_registry_docs'] = 1;
		$this->property['comments'] = $faker->text(100);
		$this->property['status'] = 2;
		$this->property['created_by'] = "Administrator";
		$this->property['updated_by'] = "";

		$this->property['remove_requirements'] = array();
		$this->property['remove_requirements']['property_type'] = array(2=>"Terraced");
		$this->property['remove_requirements']['bedrooms'] = array(6=>"1 bedroom");
		$this->property['remove_requirements']['floors'] = array(36=>"1 floor");
		$this->property['remove_requirements']['furnitures'] = array(14=>"Part furnished");
		$this->property['remove_requirements']['garden_details'] = array(21=>"Fenced and secure",
															23=>"Back garden",
															25=>"Communal garden");

		$faker->seed(20100); // Seed
		$this->company['name'] = $faker->company;
		$this->company['phone'] = $faker->phoneNumber;
		$this->company['email'] = $faker->email;
		$this->company['website'] = $faker->url;
		$this->company['address'] = $faker->streetAddress;
		$this->company['postcode'] = $faker->postcode;
		$this->company['town'] = $faker->city;

		$faker->seed(20200); // Seed
		$this->property2['address'] = $faker->streetAddress;
		$this->property2['postcode'] = $faker->postcode;
		$this->property2['town'] = $faker->city;
		$this->property2['available_date'] = date("d/m/Y", strtotime("next year"));
		$this->property2['requirements_on'] = array();
		$this->property2['requirements_on']['property_type'] = array(1=>"Detached",
																	3=>"Flats/Apartments",
																	4=>"Semi-detached",
																	5=>"Bungalow");
		$this->property2['requirements_on']['bedrooms'] = array(7=>"2 bedrooms",
																8=>"3 bedrooms",
																9=>"4 bedrooms",
																10=>"5 bedrooms",
																11=>"6 bedrooms");
		$this->property2['requirements_on']['floors'] = array(37=>"2 floors",
															38=>"3 floors",
															39=>"4 or more floors");
		$this->property2['requirements_on']['furnitures'] = array(12=>"Furnished",
																13=>"Unfurnished");
		$this->property2['requirements_on']['garden_details'] = array(20=>"Front garden",
															22=>"Large back garden",
															24=>"Private/not overlooked");
		$this->property2['requirements_off'] = array();
		$this->property2['requirements_off']['property_type'] = array(2=>"Terraced");
		$this->property2['requirements_off']['bedrooms'] = array(6=>"1 bedroom");
		$this->property2['requirements_off']['floors'] = array(36=>"1 floor");
		$this->property2['requirements_off']['furnitures'] = array(14=>"Part furnished");
		$this->property2['requirements_off']['garden_details'] = array(21=>"Fenced and secure",
															23=>"Back garden",
															25=>"Communal garden");
		$this->property2['previous_crimes_description'] = "";
		$this->property2['special_design_features'] = $faker->text(250);
		$this->property2['value'] = $faker->numberBetween(150000,500000);
		$this->property2['valuation_date'] = $faker->dateTimeBetween("5 years ago", "now")->format("d/m/Y");
		$this->property2['target_rent'] = $faker->numberBetween(350,600);

		$this->property2['accessible'] = 27;
		$this->property2['willing_to_adapt'] = 35;
		$this->property2['smoking'] = 30;
		$this->property2['pets'] = 32;
		$this->property2['previous_crimes_near'] = 0;

		$this->property2['mortgage_outstanding'] = 0;
		$this->property2['buy_to_let'] = 0;
		$this->property2['land_registry_docs'] = 0;
		$this->property2['comments'] = $faker->text(100);
		$this->property2['status'] = 1;
		$this->property2['created_by'] = "Administrator";
		$this->property2['updated_by'] = "Dummy user";

        //$this->waitForUserInput();

    }

    public function testCreateProperty()
    {
    	$this->loginAs('admin');

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-property')
		  )
		);

		// Click in btn-add-property
		$this->webDriver->findElement(\WebDriverBy::id('btn-add-property'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-save-property')
		  )
		);

		// Landlord
		$landlordField = $this->webDriver->findElement(\WebDriverBy::id('combobox-landlord'));
		$landlordField->sendKeys("a");

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('ui-menu-item')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('.ui-autocomplete li:nth-of-type(1)'))->click();

		// Create local authority
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn:nth-child(2)'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('organisation_name')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('organisation_type'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('organisation_name'))->clear()->sendKeys($this->company['name']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_phone'))->clear()->sendKeys($this->company['phone']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_email'))->clear()->sendKeys($this->company['email']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_website'))->clear()->sendKeys($this->company['website']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_address'))->clear()->sendKeys($this->company['address']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_postcode'))->clear()->sendKeys($this->company['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('organisation_town'))->clear()->sendKeys($this->company['town']);

		// Save local authority
		$this->webDriver->findElement(\WebDriverBy::cssSelector('button.btn-success'))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('property_status')
		  )
		);

		// Datos del contacto
		$this->webDriver->findElement(\WebDriverBy::id('property_address'))->sendKeys($this->property['address']);
		$this->webDriver->findElement(\WebDriverBy::id('property_postcode'))->sendKeys($this->property['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('property_town'))->sendKeys($this->property['town']);
		$this->webDriver->findElement(\WebDriverBy::id('property_available_date'))->clear()->sendKeys($this->property['available_date']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::invisibilityOfElementLocated(
		    \WebDriverBy::id('ui-datepicker-div')
		  )
		);

		foreach ($this->property['requirements_on']['property_type'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}
		foreach ($this->property['requirements_on']['bedrooms'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$action = new \WebDriverActions($this->webDriver);
		$element = $this->webDriver->findElement(\WebDriverBy::id("btn-save-property"));
		$action->moveToElement($element)->perform();
		$element = $this->webDriver->findElement(\WebDriverBy::cssSelector("#housing-requirements-fieldset > div:nth-child(4)"));
		$action->moveToElement($element)->perform();

		foreach ($this->property['requirements_on']['floors'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}
		foreach ($this->property['requirements_on']['furnitures'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[parking]' and @value=16]"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('property_parking_for'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		foreach ($this->property['requirements_on']['garden_details'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[accessible]' and @value={$this->property['accessible']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[willing_to_adapt]' and @value={$this->property['willing_to_adapt']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[smoking]' and @value={$this->property['smoking']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[pets]' and @value={$this->property['pets']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[previous_crimes_near]' and @value={$this->property['previous_crimes_near']}]"))->click();

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('property_previous_crimes_description')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::id('property_previous_crimes_description'))->sendKeys($this->property['previous_crimes_description']);
		$this->webDriver->findElement(\WebDriverBy::id('property_special_design_features'))->sendKeys($this->property['special_design_features']);

		$this->webDriver->findElement(\WebDriverBy::id('property_value'))->sendKeys($this->property['value']);
		$this->webDriver->findElement(\WebDriverBy::id('property_valuation_date'))->clear()->sendKeys($this->property['valuation_date']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);
		$this->webDriver->findElement(\WebDriverBy::id('property_target_rent'))->sendKeys($this->property['target_rent']);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-property'))->getLocationOnScreenOnceScrolledIntoView();
		$this->webDriver->findElement(\WebDriverBy::id('property_target_rent'))->getLocationOnScreenOnceScrolledIntoView();

		//$this->waitForUserInput();

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[mortgage_outstanding]' and @value={$this->property['mortgage_outstanding']}]"))->click();
		if($this->property['mortgage_outstanding'])
			$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[buy_to_let]' and @value={$this->property['buy_to_let']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[land_registry_docs]' and @value={$this->property['land_registry_docs']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('property_comments'))->sendKeys($this->property['comments']);
		$this->webDriver->findElement(\WebDriverBy::id('property_status'))->findElement(\WebDriverBy::cssSelector("option[value='{$this->property['status']}']"))->click();

		//$this->waitForUserInput();

		// Save property
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-property'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-property')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The property was created successfully', $message->getText());

    }

    public function testVerifyPropertyCreated()
    {
		$this->loginAs('admin');
        $this->verifyRecord($this->property);
    }

    public function testModifyProperty()
    {
		$this->loginAs('user');

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-property')
		  )
		);

		// Modify data
		$this->webDriver->findElement(\WebDriverBy::id('property_address'))->clear()->sendKeys($this->property2['address']);
		$this->webDriver->findElement(\WebDriverBy::id('property_postcode'))->clear()->sendKeys($this->property2['postcode']);
		$this->webDriver->findElement(\WebDriverBy::id('property_town'))->clear()->sendKeys($this->property2['town']);
		$this->webDriver->findElement(\WebDriverBy::id('property_available_date'))->clear()->sendKeys($this->property2['available_date']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);

		foreach ($this->property['remove_requirements']['property_type'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}
		foreach ($this->property['remove_requirements']['bedrooms'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}
		foreach ($this->property['remove_requirements']['floors'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-property'))->getLocationOnScreenOnceScrolledIntoView();
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#housing-requirements-fieldset > div:nth-child(4)'))->getLocationOnScreenOnceScrolledIntoView();

		foreach ($this->property['remove_requirements']['furnitures'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[parking]' and @value=16]"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('property_parking_for'))->findElement(\WebDriverBy::cssSelector("option[value='2']"))->click();

		foreach ($this->property['remove_requirements']['garden_details'] as $key => $item)
		{
			$this->webDriver->findElement(\WebDriverBy::xpath("//fieldset[4]/*/*/*/*/label[contains(text(),'".$item."')]"))->click();
		}

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[accessible]' and @value={$this->property2['accessible']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[willing_to_adapt]' and @value={$this->property2['willing_to_adapt']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[smoking]' and @value={$this->property2['smoking']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[pets]' and @value={$this->property2['pets']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[previous_crimes_near]' and @value={$this->property2['previous_crimes_near']}]"))->click();

		$this->webDriver->findElement(\WebDriverBy::id('property_special_design_features'))->clear()->sendKeys($this->property2['special_design_features']);

		$this->webDriver->findElement(\WebDriverBy::id('property_value'))->clear()->sendKeys($this->property2['value']);
		$this->webDriver->findElement(\WebDriverBy::id('property_valuation_date'))->clear()->sendKeys($this->property2['valuation_date']);
		$this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);
		$this->webDriver->findElement(\WebDriverBy::id('property_target_rent'))->clear()->sendKeys($this->property2['target_rent']);

		$this->webDriver->findElement(\WebDriverBy::id('btn-save-property'))->getLocationOnScreenOnceScrolledIntoView();
		$this->webDriver->findElement(\WebDriverBy::id('property_target_rent'))->getLocationOnScreenOnceScrolledIntoView();

		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[mortgage_outstanding]' and @value={$this->property2['mortgage_outstanding']}]"))->click();
		if($this->property2['mortgage_outstanding'])
			$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[buy_to_let]' and @value={$this->property2['buy_to_let']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[land_registry_docs]' and @value={$this->property2['land_registry_docs']}]"))->click();
		$this->webDriver->findElement(\WebDriverBy::id('property_comments'))->clear()->sendKeys($this->property2['comments']);
		$this->webDriver->findElement(\WebDriverBy::id('property_status'))->findElement(\WebDriverBy::cssSelector("option[value='{$this->property2['status']}']"))->click();

		// Save property
		$this->webDriver->findElement(\WebDriverBy::id('btn-save-property'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('btn-add-property')
		  )
		);

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::className('alert-success')
		  )
		);

		$message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The property was updated successfully', $message->getText());
    }

    public function testVerifyPropertyModified()
    {
		$this->loginAs('admin');
		$this->verifyRecord($this->property2);
    }

    public function testDeleteProperty()
    {
    	$this->loginAs('admin');

		// wait for add button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-property')
		  )
		);

		// wait for add button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(3)')
		  )
		);

        // Click delete
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(3)"))->click();

		// wait for add button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-delete-property')
		  )
		);

		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.text-center')
		  )
		);

        // Verify name in popup delete
        $this->assertContains($this->property2['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-property"))->click();

		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::cssSelector('.alert')
		  )
		);

		$this->assertContains('Congrats! The property was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

		sleep(1); // wait table reload

		$elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(4)"));

		// Verify record doesn't exist in table.
		$this->assertCount(0, $elements);
    }

    private function verifyRecord($property)
    {
		// Wait for back button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-add-property')
		  )
		);

        // Wait for back button is clickable
		$this->webDriver->wait(10,1000)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(1)')
		  )
		);

		/**************************/
        /*** 	Verify record     */
        /**************************/
        $this->assertEquals($property['address'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(2)"))->getText());
        $this->assertEquals($property['postcode'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(3)"))->getText());

        // Verify show view
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(1)'))->click();

        sleep(1);

        // Wait for back button is clickable
		$this->webDriver->wait(10,500)->until(
		  \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
		    \WebDriverBy::id('fields-container')
		  )
		);

		$this->assertEquals($property['address'], $this->webDriver->findElement(\WebDriverBy::id("property_address"))->getText());
		$this->assertEquals($property['postcode'], $this->webDriver->findElement(\WebDriverBy::id("property_postcode"))->getText());
		$this->assertEquals($property['town'], $this->webDriver->findElement(\WebDriverBy::id("property_town"))->getText());
		$this->assertEquals($property['created_by'], $this->webDriver->findElement(\WebDriverBy::id("property_created_by"))->getText());
		if($property['updated_by'])
			$this->assertEquals($property['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("property_updated_by"))->getText());

		foreach ($property['requirements_on'] as $requirement_name => $requirement)
		{
			foreach ($requirement as $idrequirement => $title)
			{
				$this->assertTrue($this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))?$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))->isSelected():false);
			}
		}

		foreach ($property['requirements_off'] as $requirement_name => $requirement)
		{
			foreach ($requirement as $idrequirement => $title)
			{
				$this->assertFalse($this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))?$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))->isSelected():false);
			}
		}

		// go to index (back button)
		$this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn'))->click();

		// wait for edit button is clickable
		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(2)')
		  )
		);

		// Verify edit view
		$this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody:nth-child(2) > tr:nth-child(4) > td:nth-child(5) > div:nth-child(1) > a:nth-child(2)'))->click();

		$this->webDriver->wait(10)->until(
		  \WebDriverExpectedCondition::elementToBeClickable(
		    \WebDriverBy::id('btn-save-property')
		  )
		);

		$this->assertEquals($property['address'], $this->webDriver->findElement(\WebDriverBy::id('property_address'))->getAttribute('value'));
		$this->assertEquals($property['postcode'], $this->webDriver->findElement(\WebDriverBy::id('property_postcode'))->getAttribute('value'));
		$this->assertEquals($property['town'], $this->webDriver->findElement(\WebDriverBy::id('property_town'))->getAttribute('value'));
		$this->assertEquals($property['created_by'], $this->webDriver->findElement(\WebDriverBy::id("property_created_by"))->getText());
		if($property['updated_by'])
			$this->assertEquals($property['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("property_updated_by"))->getText());

		foreach ($property['requirements_on'] as $requirement_name => $requirement)
		{
			foreach ($requirement as $idrequirement => $title)
			{
				$this->assertTrue($this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))?$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))->isSelected():false);
			}
		}

		foreach ($property['requirements_off'] as $requirement_name => $requirement)
		{
			foreach ($requirement as $idrequirement => $title)
			{
				$this->assertFalse($this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))?$this->webDriver->findElement(\WebDriverBy::xpath("//input[@name='property[".$requirement_name."]' and @value=".$idrequirement."]"))->isSelected():false);
			}
		}
    }

}