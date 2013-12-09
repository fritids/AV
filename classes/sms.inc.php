<?php

/**
 * Librairie d'envoi de SMS via POST HTTP
 *
 * Auteur Yoni Guimberteau yoni.guimberteau@gmail.com
 *
 * copyright (c) 2013 Yoni Guimberteau
 * licence : utilisation, modification, commercialisation.
 * L'auteur ainsi se dŽcharge de toute responsabilitŽ
 * concernant une quelconque utilisation de ce code, livrŽ sans aucune garanti.
 * Il n'est distribuŽ qu'ˆ titre d'exemple de fonctionnement du module POST HTTP de OCTOPUSH,
 * Vous pourrez toutefois tŽlŽcharger une version actualisŽe sur www.octopush-dm.com/sms-api
 */

define('DOMAIN', 'http://www.octopush-dm.com');
define('PORT', '80');
define('PATH_SMS', '/api/sms');
define('PATH_BALANCE', '/api/balance');

define('QUALITE_STANDARD', 'XXX');
define('QUALITE_PRO', 'FR');

define('INSTANTANE', 1);
define('DIFFERE', 2);

define('SIMULATION', 'simu');
define('REEL', 'real');

class SMS
{
	public $user_login; // string
	public $api_key;   // string
	public $sms_text; // string
	public $sms_recipients;  // array

	public $recipients_first_names;  // array
	public $recipients_last_names;  // array
	public $sms_fields_1;  // array
	public $sms_fields_2;  // array
	public $sms_fields_3;  // array
	public $sms_mode;  // int (instantanŽ ou diffŽrŽ)
	public $sms_type;  // int (standard ou pro)
	public $sms_d;	// int
	public $sms_m;  // int
	public $sms_h;  // int
	public $sms_i;  // int
	public $sms_y;  // int
	public $sms_sender;   // string
	public $request_mode;   // string
	public $sms_ticket;   // string

	public function __construct()
	{
		$this->user_login = '';
		$this->api_key = '';

		$this->sms_text = '';

		$this->sms_recipients = array();
		$this->recipients_first_names = array();
		$this->recipients_last_names = array();
		$this->sms_fields_1 = array();
		$this->sms_fields_2 = array();
		$this->sms_fields_3 = array();

		$this->sms_mode = INSTANTANE;
		$this->sms_d = date('d');
		$this->sms_m = date('m');
		$this->sms_h = date('H');
		$this->sms_i = date('i');
		$this->sms_y = date('Y');

		$this->sms_sender = 'CampagneSMS';
		$this->sms_type = QUALITE_STANDARD;
		$this->sms_mode = INSTANTANE;
		$this->request_mode = REEL;
	}

	function send()
	{
		$domain = DOMAIN;
		$path = PATH_SMS;
		$port = PORT;

		$data = array(
			'user_login' => $this->user_login,
			'api_key' => $this->api_key,
			'sms_text' => $this->sms_text,
			'sms_recipients' => implode(',', $this->sms_recipients),
			'recipients_first_names' => implode(',', $this->recipients_first_names),
			'recipients_last_names' => implode(',', $this->recipients_last_names),
			'sms_fields_1' => implode(',', $this->sms_fields_1),
			'sms_fields_2' => implode(',', $this->sms_fields_2),
			'sms_fields_3' => implode(',', $this->sms_fields_3),
			'sms_mode' => $this->sms_mode,
			'sms_type' => $this->sms_type,
			'sms_sender' => $this->sms_sender,
			'request_mode' => $this->request_mode
		);

		if ($this->sms_mode == DIFFERE)
		{
			$data['sms_d'] = $this->sms_d;
			$data['sms_m'] = $this->sms_m;
			$data['sms_h'] = $this->sms_h;
			$data['sms_i'] = $this->sms_i;
			$data['sms_y'] = $this->sms_y;
		}
		return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	function getBalance()
	{
		$domain = DOMAIN;
		$path = PATH_BALANCE;
		$port = PORT;

		$data = array(
			'user_login' => $this->user_login,
			'api_key' => $this->api_key
		);

		return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	private function _httpRequest($domain, $path, $port, $A_fields = array())
	{
		$ssl = $domain . $path;
		@set_time_limit(0);
		$qs = array();
		foreach ($A_fields as $k => $v)
		{
			$qs[] = $k . '=' . urlencode($v);
		}

		$request = join('&', $qs);

		if (function_exists('curl_init') AND $ch = curl_init(substr($domain, 7) . $path))
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_PORT, $port);

			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		}
		else if (ini_get('allow_url_fopen'))
		{
			$fp = fsockopen(substr($domain, 7), $port, $errno, $errstr, 100);
			if (!$fp)
			{
				echo 'Unable to connect to host. Try again later.';
				return null;
			}
			$header = "POST " . $path . " HTTP/1.1\r\n";
			$header .= 'Host: ' . substr($domain, 7) . "\r\n";
			$header .= "Accept: text\r\n";
			$header .= "User-Agent: Octopush\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($request) . "\r\n";
			$header .= "Connection: close\r\n\r\n";
			$header .= "{$request}\r\n\r\n";

			fputs($fp, $header);
			$result = '';
			while (!feof($fp))
				$result .= fgets($fp, 1024);
			fclose($fp);

			$result = substr($result, strpos($result, "\r\n\r\n") + 4);
			return $result;
		}
		else
		{
			die('Server does not support HTTP(S) requests.');
		}
		return $response;
	}

	function set_user_login($user_login)
	{
		$this->user_login = $user_login;
	}

	function set_api_key($api_key)
	{
		$this->api_key = $api_key;
	}

	function set_sms_text($sms_text)
	{
		$this->sms_text = $sms_text;
	}

	function set_sms_type($sms_type)
	{
		$this->sms_type = $sms_type;
	}

	function set_sms_recipients($sms_recipients)
	{
		$this->sms_recipients = $sms_recipients;
	}

	function set_recipients_first_names($first_names)
	{
		$this->recipients_first_names = $first_names;
	}

	function set_recipients_last_names($last_names)
	{
		$this->recipients_last_names = $last_names;
	}

	function set_sms_fields_1($sms_fields_1)
	{
		$this->sms_fields_1 = $sms_fields_1;
	}

	function set_sms_fields_2($sms_fields_2)
	{
		$this->sms_fields_2 = $sms_fields_2;
	}

	function set_sms_fields_3($sms_fields_3)
	{
		$this->sms_fields_3 = $sms_fields_3;
	}

	function set_sms_mode($sms_mode)
	{
		$this->sms_mode = $sms_mode;
	}

	function set_sms_sender($sms_sender)
	{
		$this->sms_sender = $sms_sender;
	}

	function set_date($y, $m, $d, $h, $i)
	{
		$this->sms_y = $y;
		$this->sms_d = $d;
		$this->sms_m = $m;
		$this->sms_h = $h;
		$this->sms_i = $i;
	}

	function set_simulation_mode()
	{
		$this->request_mode = SIMULATION;
	}

	function set_sms_ticket($sms_ticket)
	{
		$this->sms_ticket = $sms_ticket;
	}
}

?>
