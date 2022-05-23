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


use OCA\TFS\Exceptions\EntryNotFoundException;
use OCA\TFS\Model\Entry;
use OCA\TFS\Tools\Exceptions\InvalidItemException;
use OCA\TFS\Tools\Exceptions\RowNotFoundException;


/**
 * Class EntryRequestBuilder
 *
 * @package OCA\TFS\Db
 */
class EntryRequestBuilder extends CoreQueryBuilder {


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getEntryInsertSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_ENTRIES);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getEntryUpdateSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_ENTRIES);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getEntrySelectSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->select('e.id', 'e.unique_id', 'e.item_id', 'e.title')
		   ->from(self::TABLE_ENTRIES, 'e')
		   ->setDefaultSelectAlias('e');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return CoreRequestBuilder
	 */
	protected function getEntryDeleteSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_ENTRIES);

		return $qb;
	}


	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Entry
	 * @throws EntryNotFoundException
	 */
	public function getItemFromRequest(CoreRequestBuilder $qb): Entry {
		/** @var Entry $entry */
		try {
			$entry = $qb->asItem(Entry::class);
		} catch (InvalidItemException | RowNotFoundException $e) {
			throw new EntryNotFoundException();
		}

		return $entry;
	}

	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Entry[]
	 */
	public function getItemsFromRequest(CoreRequestBuilder $qb): array {
		return $qb->asItems(Entry::class);
	}

}

