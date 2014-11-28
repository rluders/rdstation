<?php namespace Rluders\RDStation;

use Rluders\RDStation\RDException;
use Guzzle\Http\Client as GuzzleClient;

class RDStation
{

	/**
	 * RDStation account token
	 * @type string
	 */
	protected $token = null;

	/**
	 * Page or Event identifier
	 * @var string
	 */
	protected $identifier = null;

	/**
	 * API
	 * @var string
	 */
	protected $api_url = 'http://www.rdstation.com.br/api/1.2/';
	
	/**
	 * Class constructor
	 * @param string $token      RDStation account token
	 * @param string $identifier Page or Event identifier
	 */
	public function __construct($token = null, $identifier = null)
	{

		$this->setToken($token);
		$this->setIdentifier($identifier);

	}

	/**
	 * Get RDStation API URL
	 * @return string
	 */
	public function getApiUrl()
	{

		return $this->api_url;

	}

	/**
	 * Set RDStation API URL
	 * @param string $api_url RDStation API full URL
	 */
	public function setApiUrl($api_url)
	{

		$this->api_url = $api_url;
		return $this;

	}

	/**
	 * Get account token
	 * @return string
	 */
	public function getToken()
	{

		return $this->token;

	}

	/**
	 * Set account token
	 * @param string $token RDStation account token
	 */
	public function setToken($token)
	{

		$this->token = $token;
		return $this;

	}

	/**
	 * Get page or event identifier
	 * @return string
	 */
	public function getIdentifier()
	{

		return $this->identifier;

	}

	/**
	 * Set page or event identifier
	 * @param string $identifier Set event or page identifier
	 */
	public function setIdentifier($identifier)
	{

		$this->identifier = $identifier;
		return $this;

	}

	/**
	 * Prepare data to request
	 * @param  array $data Data to sent
	 * @return string      URL-encoded query string
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
			array(
				'token_rdstation' => $this->getToken(),
				'identificador'   => $this->getIdentifier()
			)
		);

	}

	/**
	 * Send data to RDStation
	 * @param  array $data       Data to sent
	 * @param  string $identifier Page or event identifier
	 * @return boolean
	 */
	public function send($data, $identifier = null)
	{

		if ($identifier) {

			$this->setIdenfirier($identifier);

		}

		$data = $this->prepareData($data);
		
		try {

			$client = new GuzzleClient($this->getApiUrl());

			$request = $client->post(
				'conversions',
				array(
					'config' => array(
						'curl' => array(
							CURLOPT_POST => 1,
							CURLOPT_FOLLOWLOCATION => 1,
							CURLOPT_SSL_VERIFYPEER => false
						)
					)
				),
				$data
			)->send();

			return true;

		} catch (RDException $e) {

			if (ini_get('display_errors')) {

				echo $e->getMessage();

			}

			return false;

		}

	}

}