<?php

namespace App\BackendBundle\Tests\Controller;

use facebook\webdriver\lib;
use App\ApiBundle\Utils\FakeNinGenerator;


class SeleniumControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $webDriver;
    protected $url = 'http://localhost/app_test.php';

    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }

    public function setUp()
    {
        //$profile = new \FirefoxProfile();
        //$profile->addExtension('/home/jarenal/.mozilla/firefox/mcxewma5.default/extensions/firebug@software.joehewitt.com.xpi');
        //$capabilities = \DesiredCapabilities::firefox();
        //$capabilities->setCapability(\FirefoxDriver::PROFILE, $profile);
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = \RemoteWebDriver::create('http://127.0.0.1:4444/wd/hub', $capabilities);
    }

    public function tearDown()
    {
        $this->webDriver->close();
    }

    public function testBrowser()
    {
        /*
        $this->webDriver->get($this->url);

        $this->waitForUserInput();

        $username = $this->webDriver->findElement(
          \WebDriverBy::id('username')
        );

        $username->sendKeys('admin');

        $password = $this->webDriver->findElement(
          \WebDriverBy::id('password')
        );

        $password->sendKeys('1234')->submit();   */

    }

}