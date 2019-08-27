<?php

namespace App\BackendBundle\Tests\Controller;

class TicketControllerTest extends AppBaseTestCase
{
    protected $tickets = array();
    protected $url = 'http://localhost/app_test.php/ticket/list';

    public function setUp()
    {
        parent::setUp();

        $faker = \Faker\Factory::create('en_GB');

        $faker->seed(30000); // Seed
        $this->tickets[0] = array();
        $this->tickets[0]['type'] = array("id"=>"1", "name"=>"Anti-social behaviour");
        $this->tickets[0]['title'] = str_replace(array("\r", "\n"), '', $faker->text(50));
        $this->tickets[0]['description'] = str_replace(array("\r", "\n"), '', $faker->text(500));
        $this->tickets[0]['duedate_at'] = $faker->dateTimeBetween("now", "next year")->format("d/m/Y");
        $this->tickets[0]['status'] = array("id"=>"1", "name"=>"Outstanding");
        $this->tickets[0]['created_by'] = "Administrator";
        $this->tickets[0]['updated_by'] = "";

        $faker->seed(30100); // Seed
        $this->tickets[1] = array();
        $this->tickets[1]['type'] = array("id"=>"4", "name"=>"Viewings");
        $this->tickets[1]['title'] = str_replace(array("\r", "\n"), '', $faker->text(30));
        $this->tickets[1]['description'] = str_replace(array("\r", "\n"), '', $faker->text(350));
        $this->tickets[1]['duedate_at'] = $faker->dateTimeBetween("now", "next year")->format("d/m/Y");
        $this->tickets[1]['status'] = array("id"=>"3", "name"=>"Completed");
        $this->tickets[1]['created_by'] = "Administrator";
        $this->tickets[1]['updated_by'] = "Dummy user";
    }

    public function testCreateTicket()
    {
        $this->loginAs('admin');

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        // Click in btn-add-ticket
        $this->webDriver->findElement(\WebDriverBy::id('btn-add-ticket'))->click();

        $this->webDriver->wait(10)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::id('btn-save-ticket')
          )
        );

        // Fill data
        $this->fillData($this->tickets[0]);

        // Save ticket
        $this->webDriver->findElement(\WebDriverBy::id('btn-save-ticket'))->click();

        // Verify alert
        $this->webDriver->wait(10)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        $this->webDriver->wait(10)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::className('alert-success')
          )
        );

        $message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertContains('Congrats! The task was created successfully', $message->getText());
    }

    public function testVerifyTicketCreated()
    {
        $this->loginAs('admin');
        $this->verifyRecord($this->tickets[0]);
    }

    public function testModifyTicket()
    {
        $this->loginAs('user');

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('#table-javascript > tbody > tr:first-child > td:last-child > div.btn-toolbar > a.btn-edit-ticket')
          )
        );

        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody > tr:first-child > td:last-child > div.btn-toolbar > a.btn-edit-ticket'))->click();

        $this->webDriver->wait(10,1000)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::id('fields-container')
          )
        );

        $this->webDriver->wait(10,1000)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-save-ticket')
          )
        );

        // Fill data
        $this->fillData($this->tickets[1]);

        // Save ticket
        $this->webDriver->findElement(\WebDriverBy::id('btn-save-ticket'))->click();

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::className('alert-success')
          )
        );

        $message = $this->webDriver->findElement(\WebDriverBy::className('alert-success'));

        $this->assertEquals('Congrats! The task was updated successfully', $message->getText());
    }

    public function testVerifyTicketModified()
    {
        $this->loginAs('admin');
        $this->verifyRecord($this->tickets[1]);
    }

    public function testDeleteTicket()
    {
        $this->loginAs('admin');

        // wait for add button is clickable
        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        // wait for add button is clickable
        $this->webDriver->wait(10,1000)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-remove-ticket')
          )
        );

        $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-remove-ticket"))->click();

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-delete-ticket')
          )
        );

        $this->assertContains("{$this->tickets[1]['title']}", $this->webDriver->findElement(\WebDriverBy::cssSelector(".text-center"))->getText());

        // Delete record
        $this->webDriver->findElement(\WebDriverBy::cssSelector("#btn-delete-ticket"))->click();

        $this->webDriver->wait(10)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::cssSelector('.alert')
          )
        );

        sleep(1);

        $this->assertContains('Congrats! The task was deleted successfully', $this->webDriver->findElement(\WebDriverBy::cssSelector(".alert"))->getText());

        sleep(1); // wait table reload

        $elements = $this->webDriver->findElements(\WebDriverBy::cssSelector("#table-javascript > tbody:nth-child(2) > tr:nth-child(6) > td:nth-child(1)"));

        $this->assertCount(0, $elements);
    }

    private function verifyRecord($data)
    {
        $this->webDriver->wait(10,1000)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-show-ticket')
          )
        );

        /***************************************/
        /***    Verify record in LIST view     */
        /***************************************/
        $this->assertEquals($data['title'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody> tr:first-child > td:nth-child(2)"))->getText());
        $this->assertEquals($data['duedate_at'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody> tr:first-child > td:nth-child(4)"))->getText());
        $this->assertEquals($data['status']['name'], $this->webDriver->findElement(\WebDriverBy::cssSelector("#table-javascript > tbody> tr:first-child > td:nth-child(5)"))->getText());

        //$this->waitForUserInput();

        /***************************************/
        /***    Verify record in SHOW view     */
        /***************************************/
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-show-ticket'))->click();

        // Wait for back button is clickable
        $this->webDriver->wait(10)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('a.btn')
          )
        );

        // Basic details
        $this->assertEquals($data['title'], $this->webDriver->findElement(\WebDriverBy::id("ticket_title"))->getText());
        $this->assertEquals($data['description'], $this->webDriver->findElement(\WebDriverBy::id("ticket_description"))->getText());
        $this->assertEquals($data['duedate_at'], $this->webDriver->findElement(\WebDriverBy::id("ticket_duedate_at"))->getText());

        // Administration
        $this->assertEquals($data['status']['name'], $this->webDriver->findElement(\WebDriverBy::id("ticket_status"))->getText());

        $this->assertEquals($data['created_by'], $this->webDriver->findElement(\WebDriverBy::id("ticket_created_by"))->getText());
        if(isset($data['updated_by']) && $data['updated_by'])
            $this->assertEquals($data['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("ticket_updated_by"))->getText());

        // go to index (back button)
        $this->webDriver->findElement(\WebDriverBy::cssSelector('a.btn'))->click();

        // wait for add button is clickable
        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-add-ticket')
          )
        );

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-edit-ticket')
          )
        );

        /***************************************/
        /***    Verify record in EDIT view     */
        /***************************************/
        $this->webDriver->findElement(\WebDriverBy::cssSelector('#table-javascript > tbody> tr:first-child > td:last-child > div.btn-toolbar > a.btn-edit-ticket'))->click();

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::id('btn-save-ticket')
          )
        );

        // Basic details
        $this->assertEquals($data['title'], $this->webDriver->findElement(\WebDriverBy::id('ticket_title'))->getAttribute('value'));
        $this->assertEquals($data['description'], $this->webDriver->findElement(\WebDriverBy::id('ticket_description'))->getAttribute('value'));
        $this->assertEquals($data['duedate_at'], $this->webDriver->findElement(\WebDriverBy::id('ticket_duedate_at'))->getAttribute('value'));

        // Administration
        $this->assertEquals($data['status']['id'], $this->webDriver->findElement(\WebDriverBy::id('ticket_status'))->getAttribute('value'));
        $this->assertEquals($data['created_by'], $this->webDriver->findElement(\WebDriverBy::id("ticket_created_by"))->getText());
        if(isset($data['updated_by']) && $data['updated_by'])
            $this->assertEquals($data['updated_by'], $this->webDriver->findElement(\WebDriverBy::id("ticket_updated_by"))->getText());
    }

    private function fillData($data)
    {
        $this->webDriver->findElement(\WebDriverBy::id('ticket_type'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['type']['name']}')]"))->click();
        $this->webDriver->findElement(\WebDriverBy::id('ticket_title'))->clear()->sendKeys($data['title']);
        $this->webDriver->findElement(\WebDriverBy::id('ticket_description'))->clear()->sendKeys($data['description']);
        $this->webDriver->findElement(\WebDriverBy::id('ticket_duedate_at'))->clear()->sendKeys($data['duedate_at']);
        $this->webDriver->getKeyboard()->pressKey(\WebDriverKeys::ESCAPE);
        $this->webDriver->findElement(\WebDriverBy::id('ticket_status'))->findElement(\WebDriverBy::xpath("option[contains(.,'{$data['status']['name']}')]"))->click();
    }

}
