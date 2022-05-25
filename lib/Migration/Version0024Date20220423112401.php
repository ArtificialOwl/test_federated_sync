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


namespace OCA\TFS\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;


class Version0024Date20220423112401 extends SimpleMigrationStep {


	/**
	 * @param IDBConnection $connection
	 */
	public function __construct(IDBConnection $connection) {
	}


	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 *
	 * @return null|ISchemaWrapper
	 * @throws SchemaException
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$table = $schema->createTable('tfs_items');
		$table->addColumn(
			'id', 'integer', [
					'autoincrement' => true,
					'notnull' => true,
					'length' => 4,
					'unsigned' => true,
				]
		);
		$table->addColumn(
			'unique_id', 'string', [
						   'notnull' => true,
						   'length' => 31,
						   'default' => 'Unknown'
					   ]
		);
		$table->addColumn(
			'title', 'string', [
					   'notnull' => true,
					   'length' => 254,
				   ]
		);
		$table->addColumn(
			'user_id', 'string', [
						 'notnull' => true,
						 'length' => 127,
					 ]
		);
		$table->addColumn(
			'user_single_id', 'string', [
								'notnull' => true,
								'length' => 31,
							]
		);
		$table->setPrimaryKey(['id']);
		$table->addUniqueIndex(['unique_id']);

		$table = $schema->createTable('tfs_entries');
		$table->addColumn(
			'id', 'integer', [
					'autoincrement' => true,
					'notnull' => true,
					'length' => 4,
					'unsigned' => true,
				]
		);
		$table->addColumn(
			'unique_id', 'string', [
						   'notnull' => true,
						   'length' => 31
					   ]
		);
		$table->addColumn(
			'item_id', 'string', [
						 'notnull' => true,
						 'length' => 31,
					 ]
		);
		$table->addColumn(
			'title', 'string', [
					   'notnull' => true,
					   'length' => 254,
				   ]
		);
		$table->setPrimaryKey(['id']);

		$table = $schema->createTable('tfs_shares');
		$table->addColumn(
			'id', 'integer', [
					'autoincrement' => true,
					'notnull' => true,
					'length' => 4,
					'unsigned' => true,
				]
		);
		$table->addColumn(
			'item_id', 'string', [
						 'notnull' => true,
						 'length' => 15
					 ]
		);
		$table->addColumn(
			'circle_id', 'string', [
						   'notnull' => true,
						   'length' => 31,
					   ]
		);
		$table->addColumn(
			'permissions', 'integer', [
							 'notnull' => true,
							 'length' => 4
						 ]
		);

		$table->setPrimaryKey(['id']);

		return $schema;
	}
}
