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


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OCA\Circles\CirclesManager;
use OCA\TFS\Exceptions\ItemNotFoundException;
use OCA\TFS\Model\Item;
use OCA\TFS\Service\ConfigService;


/**
 * Class ItemRequest
 *
 * @package OCA\Circles\Db
 */
class ItemRequest extends ItemRequestBuilder {


	/**
	 * ItemRequest constructor.
	 *
	 * @param ConfigService $configService
	 */
	public function __construct(ConfigService $configService) {
		parent::__construct($configService);
	}


	/**
	 * @param Item $item
	 */
	public function save(Item $item): void {
		$qb = $this->getItemInsertSql();

		$qb->setValue('unique_id', $qb->createNamedParameter($item->getUniqueId()))
		   ->setValue('title', $qb->createNamedParameter($item->getTitle()))
		   ->setValue('user_id', $qb->createNamedParameter($item->getUserId()))
		   ->setValue('user_single_id', $qb->createNamedParameter($item->getUserSingleId()));

		$qb->execute();
	}


	public function insertOrUpdate(Item $item): void {
		try {
			$this->save($item);
		} catch (UniqueConstraintViolationException $e) {
			$this->update($item);
		}
	}


	public function update(Item $item): void {
		$qb = $this->getItemUpdateSql();
		$qb->set('title', $qb->createNamedParameter($item->getTitle()));

		$qb->executeStatement();
	}

	/**
	 * @param string $uniqueId
	 *
	 * @return Item
	 * @throws ItemNotFoundException
	 */
	public function getItem(string $uniqueId): Item {
		$qb = $this->getItemSelectSql();
		$qb->limitToUniqueId($uniqueId);

		return $this->getItemFromRequest($qb);
	}


	/**
	 * @param string $circleId
	 *
	 * @return Item[]
	 */
	public function getSharedToCircleId(string $circleId): array {
		$qb = $this->getItemSelectSql();
		$qb->leftJoinShares();
		$qb->limitToSingleId($circleId, 's');

		return $this->getItemsFromRequest($qb);
	}


	/**
	 * @return Item[]
	 */
	public function getItems(): array {
		$qb = $this->getItemSelectSql();

		return $this->getItemsFromRequest($qb);
	}


	/**
	 * @param string $userId
	 */
	public function filterItems(string $userId): array {
		/** @var CirclesManager $circlesManager */
		$circlesManager = \OC::$server->get(CirclesManager::class);
		$federatedUser = $circlesManager->getLocalFederatedUser($userId);

		$queryHelper = $circlesManager->getQueryHelper();
		$qb = $queryHelper->getQueryBuilder();
		$qb->select(['i.id', 'i.unique_id', 'i.title', 'i.user_id', 'i.user_single_id']);
		$qb->from(self::TABLE_ITEMS, 'i');

		$qb->leftJoin(
			'i', self::TABLE_SHARES, 's',
			$qb->expr()->eq('s.item_id', 'i.unique_id')
		);

		$queryHelper->limitToInheritedMembers(
			'circle_id',
			's',
			$federatedUser,
			false,
			['i.user_single_id']
		);

		$items = [];
		$cursor = $qb->executeQuery();
		while ($data = $cursor->fetch()) {
			$item = new Item();
			$items[] = $item->importFromDatabase($data);
		}
		$cursor->closeCursor();

		return $items;
	}

}

