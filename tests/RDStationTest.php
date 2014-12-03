<?php

use Rluders\RDStation\RDStation;

class RDStationTest extends PHPUnit_Framework_TestCase
{
	

	public function testClassExists()
	{

		$rdstation = new RDStation();
		$this->assertInstanceOf('Rluders\RDStation\RDStation', $rdstation);

	}

	public function testSetAndGetters()
	{

		$rdstation = new RDStation();

		$rdstation->setToken('newtoken');
		$this->assertEquals('newtoken', $rdstation->getToken());

		$rdstation->setApiUrl('newurl');
		$this->assertEquals('newurl', $rdstation->getApiUrl());

		$rdstation->setIdentifier('newid');
		$this->assertEquals('newid', $rdstation->getIdentifier());

	}

	public function testSend()
	{

		$token = 'invalid-token';

		$rdstation = new RDStation($token, 'phpunit-test');
		$result = $rdstation->send(
			array(
				'name' => 'Ricardo Lüders',
				'email' => 'valid@email.com'
			)
		);

		// $this->assertTrue($result);
		$this->assertFalse($result);

	}

}