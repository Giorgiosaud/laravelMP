<?php namespace Zonapro\Mercadopago;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MercadopagoServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package('zonapro/mercadopago');
		$app = $this->app;
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->app['mercadopago'] = $this->app->share(function ($app) {
				return new MPRestClient();
			});
		$this->app['mercadopago'] = $this->app->share(function ($app) {
				$mp = new MP(Config::get('mercadopago::mp_client_id'), Config::get('mercadopago::mp_secret'));
				$mp->sandbox_mode(Config::get('mercadopago::mp_sandbox_mode'));
				return $mp;
			});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('mercadopago');
	}

}
