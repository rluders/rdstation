<?php
/**
 * RDStation.php.
 *
 * This file provides a basic class to build integrations with rdstation.
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace RDStation;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Stream\Stream;

/**
 * RDStation class.
 *
 * Basic Rd Station integration class.
 *
 * @author  Ricardo Lüders <ricardo.luders@humantech.com.br>
 * @author Isaque Alves <isaquealves@gmail.com>
 *
 * @version 1.0.0
 *
 * @since 06/08/2015
 */
class RDStation
{
    const API_URL = 'http://www.rdstation.com.br/api/';

    const STAGE_LEAD_SIMPLE = 0;
    const STAGE_LEAD_QUALIFIED = 1;
    const STAGE_LEAD_CONSUMER = 2;

    /**
     * RDStation account token.
     *
     * @var string
     */
    protected $token = null;

    /**
     * Page or Event identifier.
     *
     * @var string
     */
    protected $identifier = null;

    /**
     * RDStation private token.
     *
     * @var string
     */
    protected $privateToken = null;

    /**
     * Default API Version.
     *
     * @var string
     */
    protected $defaultApiVersion = '1.2';

    /**
     * Api Version - User setting.
     *
     * @var string
     */
    protected $apiVersion = false;

    /**
     * Api URL - Used to store the full api url, including api version.
     *
     * @var string
     */
    protected $fullApiUrl = null;

    /**
     * Class constructor.
     *
     * @param string $token      RDStation account token
     * @param string $identifier Page or Event identifier
     */
    public function __construct($token = null, $privateToken = null, $identifier = null)
    {
        $this->fullApiUrl = self::API_URL.($this->apiVersion ? $this->apiVersion : $this->defaultApiVersion).'/';
        $this->setToken($token);
        $this->setPrivateToken($privateToken);
        $this->setIdentifier($identifier);
    }

    /**
     * Get RDStation API URL.
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->fullApiUrl;
    }

    public function setApiVersion($version)
    {
        $this->apiVersion = $version;

        return $this;
    }

    /**
     * Get account token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set account token.
     *
     * @param string $token RDStation account token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * RDStation private token.
     *
     * @return string
     */
    public function getPrivateToken()
    {
        return $this->privateToken;
    }

    /**
     * Set private token.
     *
     * @param string $privateToken Set RDStation private token
     */
    public function setPrivateToken($privateToken)
    {
        $this->privateToken = $privateToken;

        return $this;
    }

    /**
     * Get page or event identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set page or event identifier.
     *
     * @param string $identifier Set event or page identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Prepare data to request.
     *
     * @param array $data Data to sent
     *
     * @return string URL-encoded query string
     */
    protected function prepareData($data)
    {
        if (isset($_COOKIE['__utmz'])) {
            $data['c_utmz'] = $_COOKIE['__utmz'];
        }

        unset(
            $data['password'],
            $data['password_confirmation'],
            $data['senha'],
              $data['confirme_senha'],
              $data['captcha'],
              $data['_wpcf7'],
              $data['_wpcf7_version'],
              $data['_wpcf7_unit_tag'],
              $data['_wpnonce'],
              $data['_wpcf7_is_ajax_call']
        );

        return array_merge(
            $data,
            [
                'token_rdstation' => $this->getToken(),
                'identificador' => $this->getIdentifier(),
            ]
        );
    }

    /**
     * Send data to RDStation.
     *
     * @param array  $data       Data to sent
     * @param string $type       The type of resource to be accessed.
     *                           'generic' is used to change a lead status;
     *                           'conversions' is used to send a new lead;
     *                           'leads' is used to update a given lead
     * @param string $identifier Page or event identifier
     *
     * @return bool
     */
    public function send($data, $type = null, $identifier = null)
    {
        if ($identifier) {
            $this->setIdentifier($identifier);
        }

        switch ($type) {

            case 'generic':
                $url = $this->fullApiUrl.'/services/'.$this->privateToken.'/generic';
                $method = 'POST';
                break;
            case 'conversions':
                $url = $this->fullApiUrl.'conversions';
                $method = 'POST';
                break;
            case 'leads' :
                $url = $this->fullApiUrl.'leads'.'/'.$data['email'];
                $method = 'PUT';
                break;
            default:
                throw new Exception("Invalid 'type' argument. Type can only be 'conversions', 'leads' or 'generic'.");
        }

        $data = $this->prepareData($data);

        $client = new GuzzleClient(['base_url' => $this->getApiUrl()]);

        $request = $client->createRequest(
                $method,
                $url,
                [
                    'body' => $data,
                ]
            );

        try {
            $response = $client->send($request);

            return $response;
        } catch (\Exception $e) {
            return Stream::factory('Failed to send request to '.$url.'.');
        }
    }
}
