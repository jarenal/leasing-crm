<?php

namespace App\BackendBundle\Tests\Controller;

use facebook\webdriver\lib;

class AppBaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected $webDriver;
    protected $url;

    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }

    public function setUp()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        /*
        $profile = new \FirefoxProfile();
        $profile->setPreference(
          'browser.startup.homepage',
          $this->url
        );
        $capabilities = \DesiredCapabilities::firefox();
        $capabilities->setCapability(\FirefoxDriver::PROFILE, $profile);
        */
        $this->webDriver = \RemoteWebDriver::create('http://127.0.0.1:4444/wd/hub', $capabilities);
        //$window = new \WebDriverDimension(1920,1024);
        //$this->webDriver->manage()->window()->setSize($window);
        $this->webDriver->manage()->window()->maximize();
        $this->webDriver->get($this->url);

    }

    public function tearDown()
    {
        $this->webDriver->close();
    }

    protected function loginAs($role)
    {
        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            \WebDriverBy::cssSelector('.form-signin')
          )
        );

        $this->webDriver->wait(10,500)->until(
            \WebDriverExpectedCondition::elementToBeClickable(
            \WebDriverBy::cssSelector('.btn')
            )
        );

    	$user = array();
    	$user['admin'] = array('username'=>'admin', 'password'=>'1234');
    	$user['user'] = array('username'=>'cbennett', 'password'=>'1234');

		$this->webDriver->findElement(\WebDriverBy::id('username'))->sendKeys($user[$role]['username']);
		$this->webDriver->findElement(\WebDriverBy::id('password'))->sendKeys($user[$role]['password'])->submit();

        $this->webDriver->wait(10,500)->until(
          \WebDriverExpectedCondition::invisibilityOfElementLocated(
            \WebDriverBy::id('loading-modal')
          )
        );
    }
}