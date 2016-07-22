<?php

use RDStation\RDStation;

class RDStationTest extends PHPUnit_Framework_TestCase
{
    public function testClassExists()
    {
        $rdstation = new RDStation();
        $this->assertInstanceOf('RDStation\RDStation', $rdstation);
    }

    public function testSetAndGetters()
    {
        $rdstation = new RDStation();

        $rdstation->setToken('newtoken');
        $this->assertEquals('newtoken', $rdstation->getToken());

        $rdstation->setPrivateToken('newprivatetoken');
        $this->assertEquals('newprivatetoken', $rdstation->getPrivateToken());

        $rdstation->setIdentifier('newid');
        $this->assertEquals('newid', $rdstation->getIdentifier());
    }

    public function testCreateNewLead()
    {
        $token = 'invalid-token';

        $rdstation = new RDStation($token, 'phpunit-test');
        $result = $rdstation->send(
            [
                'name' => 'Test User',
                'email' => 'valid@email.com',
            ], 'conversions'
        );

        //$this->assertEquals(200, $result->getStatusCode()); // only if a valid token is provided.
        $this->assertContains('Failed to send request', $result->getContents());
    }

    public function testUpdateLead()
    {
        $token = 'invalid-token';
        $privateToken = 'invalid-private-token';

        $rdstation = new RDStation($token, 'phpunit-test');
        $result = $rdstation->send(
            [
                'auth_token' => $privateToken,
                'email' => 'valid@email.com',
                'tags' => 'test',
                'lead' => [
                    'lifecycle_stage' => 1,
                    'opportunity' => true,
                ],
            ], 'leads'
        );
        //$this->assertEquals(200, $result->getStatusCode()); // only if a valid token is provided.
        $this->assertContains('Failed to send request', $result->getContents());
    }
}
