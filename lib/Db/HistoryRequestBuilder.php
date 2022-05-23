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
use OCA\TFS\Model\History;


/**
 * Class HistoryRequestBuilder
 *
 * @package OCA\TFS\Db
 */
class HistoryRequestBuilder extends CoreQueryBuilder {


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getHistoryInsertSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_HISTORY);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getHistoryUpdateSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_HISTORY);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getHistorySelectSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->select('h.id', 'h.line', 'h.single_id')
		   ->from(self::TABLE_HISTORY, 'h')
		   ->setDefaultSelectAlias('h');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return CoreRequestBuilder
	 */
	protected function getHistoryDeleteSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_HISTORY);

		return $qb;
	}


	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return History
	 * @throws RowNotFoundException
	 */
	public function getItemFromRequest(CoreRequestBuilder $qb): History {
		/** @var History $history */
		$history = $qb->asItem(History::class);

		return $history;
	}

	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return History[]
	 */
	public function getItemsFromRequest(CoreRequestBuilder $qb): array {
		return $qb->asItems(History::class);
	}

}

