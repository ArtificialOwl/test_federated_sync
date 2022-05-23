<?php

declare(strict_types=1);


/**
 * Testing Federated Sync
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2022
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


namespace OCA\TFS\Db;


use OC\DB\Connection;
use OC\DB\SchemaWrapper;
use OCA\TFS\AppInfo\Application;
use OCA\TFS\Service\ConfigService;


/**
 *
 */
class CoreQueryBuilder {


	const TABLE_ITEMS = 'tfs_items';
	const TABLE_ENTRIES = 'tfs_entries';
	const TABLE_SHARES = 'tfs_shares';
	const TABLE_HISTORY = 'tfs_history';

	protected ConfigService $configService;

	private array $tables = [
		self::TABLE_ITEMS,
		self::TABLE_ENTRIES,
		self::TABLE_SHARES,
		self::TABLE_HISTORY
	];


	/**
	 * @param ConfigService $configService
	 */
	public function __construct(ConfigService $configService) {
		$this->configService = $configService;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	public function getQueryBuilder(): CoreRequestBuilder {
		return new CoreRequestBuilder();
	}


	/**
	 *
	 */
	public function cleanDatabase(): void {
		foreach ($this->tables as $table) {
			$qb = $this->getQueryBuilder();
			$qb->delete($table);
			$qb->execute();
		}
	}


	/**
	 *
	 */
	public function uninstall(): void {
		$this->uninstallAppTables();
		$this->uninstallFromMigrations();
		$this->configService->unsetAppConfig();
	}

	/**
	 * this just empty all tables from the app.
	 */
	public function uninstallAppTables() {
		$dbConn = \OC::$server->get(Connection::class);
		$schema = new SchemaWrapper($dbConn);

		foreach ($this->tables as $table) {
			if ($schema->hasTable($table)) {
				$schema->dropTable($table);
			}
		}

		$schema->performDropTableCalls();
	}


	/**
	 *
	 */
	public function uninstallFromMigrations() {
		$qb = $this->getQueryBuilder();
		$qb->delete('migrations');
		$qb->limit('app', Application::APP_ID);

		$qb->execute();
	}

}

