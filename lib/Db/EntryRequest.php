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


use OCA\TFS\Tools\Exceptions\RowNotFoundException;
use OCA\TFS\Model\Entry;


/**
 * Class EntryRequest
 *
 * @package OCA\Circles\Db
 */
class EntryRequest extends EntryRequestBuilder {


	/**
	 * @param Entry $entry
	 */
	public function save(Entry $entry): void {
		$qb = $this->getEntryInsertSql();

		$qb->setValue('unique_id', $qb->createNamedParameter($entry->getUniqueId()))
		   ->setValue('title', $qb->createNamedParameter($entry->getTitle()))
		   ->setValue('item_id', $qb->createNamedParameter($entry->getItemId()));

		$qb->execute();
	}


	/**
	 * @param string $uniqueId
	 *
	 * @return Entry
	 * @throws RowNotFoundException
	 */
	public function getEntry(string $uniqueId): Entry {
		$qb = $this->getEntrySelectSql();
		$qb->limitToUniqueId($uniqueId);

		return $this->getItemFromRequest($qb);
	}


	/**
	 * @param string $itemId
	 *
	 * @return Entry[];
	 */
	public function getForItem(string $itemId): array {
		$qb = $this->getEntrySelectSql();
		$qb->limitToItemId($itemId);

		return $this->getItemsFromRequest($qb);
	}

}

