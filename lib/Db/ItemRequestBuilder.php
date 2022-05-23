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


use OCA\TFS\Exceptions\ItemNotFoundException;
use OCA\TFS\Model\Item;
use OCA\TFS\Tools\Exceptions\InvalidItemException;
use OCA\TFS\Tools\Exceptions\RowNotFoundException;


/**
 * Class ItemRequestBuilder
 *
 * @package OCA\TFS\Db
 */
class ItemRequestBuilder extends CoreQueryBuilder {


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getItemInsertSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_ITEMS);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getItemUpdateSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_ITEMS);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getItemSelectSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->select('i.id', 'i.unique_id', 'i.title', 'i.user_id', 'i.user_single_id')
		   ->from(self::TABLE_ITEMS, 'i')
		   ->setDefaultSelectAlias('i');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return CoreRequestBuilder
	 */
	protected function getItemDeleteSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_ITEMS);

		return $qb;
	}


	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Item
	 * @throws ItemNotFoundException
	 */
	public function getItemFromRequest(CoreRequestBuilder $qb): Item {
		/** @var Item $item */
		try {
			$item = $qb->asItem(Item::class);
		} catch (InvalidItemException | RowNotFoundException $e) {
			throw new ItemNotFoundException();
		}

		return $item;
	}

	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Item[]
	 */
	public function getItemsFromRequest(CoreRequestBuilder $qb): array {
		return $qb->asItems(Item::class);
	}

}

