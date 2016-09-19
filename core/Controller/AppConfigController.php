<?php
/**
 * @copyright Copyright (c) 2016 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OC\Core\Controller;


use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IAppConfig;
use OCP\IConfig;
use OCP\IRequest;

class AppConfigController extends Controller {

	/** @var IAppConfig */
	protected $config;

	/** @var IConfig */
	protected $appConfig;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 * @param IAppConfig $appConfig
	 */
	public function __construct($appName,
								IRequest $request,
								IConfig $config,
								IAppConfig $appConfig) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->appConfig = $appConfig;
	}

	/**
	 * @param string $app
	 * @param string $key
	 * @param string $defaultValue
	 * @return DataResponse
	 */
	public function getValue($app, $key, $defaultValue = '') {
		$this->verifyAppId($app);
		return new DataResponse([
			'data' => $this->config->getAppValue($app, $key, $defaultValue),
		]);
	}

	/**
	 * @PasswordConfirmationRequired
	 * @param string $app
	 * @param string $key
	 * @param string $value
	 * @return DataResponse
	 */
	public function setValue($app, $key, $value) {
		$this->verifyAppId($app);

		if ($app === 'core' && (strpos($key, 'public_') === 0 || strpos($key, 'remote_') === 0)) {
			return new DataResponse([
				'data' => [
					'message' => 'Unexpected error!'
				],
			], Http::STATUS_FORBIDDEN);
		}

		$this->config->setAppValue($app, $key, $value);
		return new DataResponse();
	}

	/**
	 * @return DataResponse
	 */
	public function getApps() {
		return new DataResponse([
			'data' => $this->appConfig->getApps(),
		]);
	}

	/**
	 * @param string $app
	 * @return DataResponse
	 */
	public function getKeys($app) {
		$this->verifyAppId($app);
		return new DataResponse([
			'data' => $this->config->getAppKeys($app),
		]);
	}

	/**
	 * @param string $app
	 * @param string $key
	 * @return DataResponse
	 */
	public function hasKey($app, $key) {
		$this->verifyAppId($app);
		return new DataResponse([
			'data' => $this->config->hasKey($app, $key),
		]);
	}

	/**
	 * @PasswordConfirmationRequired
	 * @param string $app
	 * @param string $key
	 * @return DataResponse
	 */
	public function deleteKey($app, $key) {
		$this->verifyAppId($app);
		return new DataResponse([
			'data' => $this->config->deleteAppValue($app, $key),
		]);
	}

	/**
	 * @PasswordConfirmationRequired
	 * @param string $app
	 * @return DataResponse
	 */
	public function deleteApp($app) {
		$this->verifyAppId($app);
		return new DataResponse([
			'data' => $this->config->deleteAppValues($app),
		]);
	}

	protected function verifyAppId($app) {
		if (\OC_App::cleanAppId($app) !== $app) {
			throw new \InvalidArgumentException('Invalid app id given');
		}
	}
}
