<?php

namespace Zonapro\Mercadopago;
/**
 * MercadoPago Integration Library
 * Access MercadoPago for payments integration
 *
 * @author hcasatti
 *
 */
$GLOBALS["LIB_LOCATION"] = dirname(__FILE__);

class MP {

	const version               = "0.2.1";
	private $preference_sandbox = False;
	private $client_id;
	private $client_secret;
	private $access_data;
	private $sandbox = FALSE;

	function __construct($client_id, $client_secret) {
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
	}

	public function sandbox_mode($enable = NULL) {
		if (!is_null($enable)) {
			$this->sandbox = $enable === TRUE;
		}

		return $this->sandbox;
	}

	public function text_mp_sandbox_mode() {
		if ($this->sandbox) {
			$this->preference_sandbox = 'sandbox_init_point';
		} else {
			$this->preference_sandbox = 'init_point';
		}
		return $this->preference_sandbox;
	}
	/**
	 * Get Access Token for API use
	 */
	public function get_access_token() {
		$app_client_values = $this->build_query(array(
				'client_id'     => $this->client_id,
				'client_secret' => $this->client_secret,
				'grant_type'    => 'client_credentials',
			));

		$access_data = MPRestClient::post("/oauth/token", $app_client_values, "application/x-www-form-urlencoded");

		$this->access_data = $access_data['response'];

		return $this->access_data['access_token'];
	}

	/**
	 * Get information for specific payment
	 * @param int $id
	 * @return array(json)
	 */
	public function get_payment($id) {
		$access_token = $this->get_access_token();

		$uri_prefix = $this->sandbox?"/sandbox":"";

		$payment_info = MPRestClient::get($uri_prefix."/collections/notifications/".$id."?access_token=".$access_token);
		return $payment_info;
	}
	public function get_payment_info($id) {
		return $this->get_payment($id);
	}

	/**
	 * Get information for specific authorized payment
	 * @param id
	 * @return array(json)
	 */
	public function get_authorized_payment($id) {
		$access_token = $this->get_access_token();

		$authorized_payment_info = MPRestClient::get("/authorized_payments/".$id."?access_token=".$access_token);
		return $authorized_payment_info;
	}

	/**
	 * Refund accredited payment
	 * @param int $id
	 * @return array(json)
	 */
	public function refund_payment($id) {
		$access_token = $this->get_access_token();

		$refund_status = array(
			"status" => "refunded",
		);

		$response = MPRestClient::put("/collections/".$id."?access_token=".$access_token, $refund_status);
		return $response;
	}

	/**
	 * Cancel pending payment
	 * @param int $id
	 * @return array(json)
	 */
	public function cancel_payment($id) {
		$access_token = $this->get_access_token();

		$cancel_status = array(
			"status" => "cancelled",
		);

		$response = MPRestClient::put("/collections/".$id."?access_token=".$access_token, $cancel_status);
		return $response;
	}

	/**
	 * Cancel preapproval payment
	 * @param int $id
	 * @return array(json)
	 */
	public function cancel_preapproval_payment($id) {
		$access_token = $this->get_access_token();

		$cancel_status = array(
			"status" => "cancelled",
		);

		$response = MPRestClient::put("/preapproval/".$id."?access_token=".$access_token, $cancel_status);
		return $response;
	}

	/**
	 * Search payments according to filters, with pagination
	 * @param array $filters
	 * @param int $offset
	 * @param int $limit
	 * @return array(json)
	 */
	public function search_payment($filters, $offset = 0, $limit = 0) {
		$access_token = $this->get_access_token();

		$filters["offset"] = $offset;
		$filters["limit"]  = $limit;

		$filters = $this->build_query($filters);

		$uri_prefix = $this->sandbox?"/sandbox":"";

		$collection_result = MPRestClient::get($uri_prefix."/collections/search?".$filters."&access_token=".$access_token);
		return $collection_result;
	}

	/**
	 * Create a checkout preference
	 * @param array $preference
	 * @return array(json)
	 */
	public function create_preference($preference) {
		$access_token = $this->get_access_token();

		$preference_result = MPRestClient::post("/checkout/preferences?access_token=".$access_token, $preference);
		return $preference_result;
	}

	/**
	 * Update a checkout preference
	 * @param string $id
	 * @param array $preference
	 * @return array(json)
	 */
	public function update_preference($id, $preference) {
		$access_token = $this->get_access_token();

		$preference_result = MPRestClient::put("/checkout/preferences/{$id}?access_token=" .$access_token, $preference);
		return $preference_result;
	}

	/**
	 * Get a checkout preference
	 * @param string $id
	 * @return array(json)
	 */
	public function get_preference($id) {
		$access_token = $this->get_access_token();

		$preference_result = MPRestClient::get("/checkout/preferences/{$id}?access_token=" .$access_token);
		return $preference_result;
	}

	/**
	 * Create a preapproval payment
	 * @param array $preapproval_payment
	 * @return array(json)
	 */
	public function create_preapproval_payment($preapproval_payment) {
		$access_token = $this->get_access_token();

		$preapproval_payment_result = MPRestClient::post("/preapproval?access_token=".$access_token, $preapproval_payment);
		return $preapproval_payment_result;
	}

	/**
	 * Get a preapproval payment
	 * @param string $id
	 * @return array(json)
	 */
	public function get_preapproval_payment($id) {
		$access_token = $this->get_access_token();

		$preapproval_payment_result = MPRestClient::get("/preapproval/{$id}?access_token=" .$access_token);
		return $preapproval_payment_result;
	}

	/**
	 * Update a preapproval payment
	 * @param string $preapproval_payment, $id
	 * @return array(json)
	 */

	public function update_preapproval_payment($id, $preapproval_payment) {
		$access_token = $this->get_access_token();

		$preapproval_payment_result = MPRestClient::put("/preapproval/".$id."?access_token=".$access_token, $preapproval_payment);
		return $preapproval_payment_result;
	}

	/* **************************************************************************************** */

	private function build_query($params) {
		if (function_exists("http_build_query")) {
			return http_build_query($params, "", "&");
		} else {
			foreach ($params as $name => $value) {
				$elements[] = "{$name}=" .urlencode($value);
			}

			return implode("&", $elements);
		}
	}

}