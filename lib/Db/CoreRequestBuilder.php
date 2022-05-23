<?php

declare(strict_types=1);


/**
 * Testing Federated Sync
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@pontapreta.net>
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


use Doctrine\DBAL\Query\QueryBuilder;
use OCA\TFS\Tools\Db\ExtendedQueryBuilder;

class CoreRequestBuilder extends ExtendedQueryBuilder {

	public function __construct() {
		parent::__construct();
	}


	/**
	 * @param string $id
	 */
	public function limitToCircleId(string $id): void {
		$this->limit('circle_id', $id);
	}


	/**
	 * @param string $itemId
	 */
	public function limitToItemId(string $itemId): void {
		$this->limit('item_id', $itemId);
	}


	/**
	 * @param string $id
	 * @param string $alias
	 */
	public function limitToSingleId(string $id, string $alias = ''): void {
		$this->limit('single_id', $id);
	}

	/**
	 * @param int $last
	 */
	public function startingAt(int $last): void {
		$this->andWhere($this->expr()->gt('id', $this->createNamedParameter($last)));
	}


	/**
	 *
	 */
	public function leftJoinShares(): void {
		if ($this->getType() !== QueryBuilder::SELECT) {
			return;
		}

		$expr = $this->expr();

		$alias = 's';
		$this->leftJoin(
			$this->getDefaultSelectAlias(), CoreQueryBuilder::TABLE_SHARES, $alias,
			$expr->eq($alias . '.unique_id', $this->getDefaultSelectAlias() . '.item_id')
		);
	}

}

