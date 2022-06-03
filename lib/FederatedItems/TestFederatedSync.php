<?php

declare(strict_types=1);


/**
 * Testing Federated Sync
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
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


namespace OCA\TFS\FederatedItems;


use OCA\Circles\IFederatedPartialSyncManager;
use OCA\Circles\IFederatedSyncManager;
use OCA\Circles\IFederatedUser;
use OCA\TFS\AppInfo\Application;
use OCA\TFS\Db\EntryRequest;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\Db\ShareRequest;
use OCA\TFS\Exceptions\ItemNotFoundException;
use OCA\TFS\Model\Entry;
use OCA\TFS\Model\Item;
use OCA\TFS\Model\Share;
use OCA\TFS\Tools\Traits\TArrayTools;
use OCA\TFS\Tools\Traits\TDeserialize;


class TestFederatedSync implements
	IFederatedSyncManager,
	IFederatedPartialSyncManager {

	use TArrayTools;
	use TDeserialize;

	const ITEM_TYPE = 'item';

	private ShareRequest $shareRequest;
	private ItemRequest $itemRequest;
	private EntryRequest $entryRequest;


	public function __construct(
		ShareRequest $shareRequest,
		ItemRequest $itemRequest,
		EntryRequest $entryRequest
	) {
		$this->shareRequest = $shareRequest;
		$this->itemRequest = $itemRequest;
		$this->entryRequest = $entryRequest;
	}


	/**
	 * @return string
	 */
	public function getAppId(): string {
		return Application::APP_ID;
	}


	/**
	 * @return string
	 */
	public function getItemType(): string {
		return self::ITEM_TYPE;
	}

	/**
	 * @return int
	 */
	public function getApiVersion(): int {
		return 1;
	}

	/**
	 * @return int
	 */
	public function getApiLowerBackCompatibility(): int {
		return 1;
	}

	/**
	 * @return bool
	 */
	public function isFullSupport(): bool {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	public function serializeItem(string $itemId): array {
		$item = $this->getCompleteItem($itemId);

		return $this->serialize($item);
	}


	/**
	 * @inheritdoc
	 */
	public function syncItem(string $itemId, array $serializedData): void {
		/** @var Item $item */
		$item = $this->deserialize($serializedData, Item::class);

		$this->itemRequest->insertOrUpdate($item);

		$this->entryRequest->removeEntriesFromItem($item->getUniqueId());
		$this->entryRequest->saveAll($item->getEntries());
	}


	/**
	 * @inheritdoc
	 */
	public function itemExists(string $itemId): bool {
		try {
			$this->itemRequest->getItem($itemId);
		} catch (ItemNotFoundException $e) {
			return false;
		}

		return true;
	}


	/**
	 * @param string $itemId
	 *
	 * @return string
	 * @throws ItemNotFoundException
	 */
	public function getOwner(string $itemId): string {
		$item = $this->itemRequest->getItem($itemId);

		return $item->getUserSingleId();
	}


	/**
	 * @inheritdoc
	 */
	public function isShareCreatable(
		string $itemId,
		string $circleId,
		array $extraData,
		IFederatedUser $federatedUser
	): bool {
//		echo '___' . json_encode($federatedUser, JSON_PRETTY_PRINT) . "\n";

		return true;
	}


	/**
	 * @inheritdoc
	 */
	public function onShareCreation(
		string $itemId,
		string $circleId,
		array $extraData,
		IFederatedUser $federatedUser
	): void {
		$share = new Share();
		$share->setItemId($itemId)
			  ->setCircleId($circleId)
			  ->setPermissions($this->getInt('permission', $extraData));

		$this->shareRequest->save($share);
	}


	/**
	 * @inheritdoc
	 */
	public function isShareModifiable(
		string $itemId,
		string $circleId,
		array $extraData,
		IFederatedUser $federatedUser
	): bool {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	public function onShareModification(
		string $itemId,
		string $circleId,
		array $extraData,
		IFederatedUser $federatedUser
	): void {
	}


	/**
	 * @inheritdoc
	 */
	public function isShareDeletable(
		string $itemId,
		string $circleId,
		IFederatedUser $federatedUser
	): bool {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	public function onShareDeletion(string $itemId, string $circleId, IFederatedUser $federatedUser): void {
	}

	/**
	 * @inheritdoc
	 */
	public function isItemModifiable(
		string $itemId,
		string $updateType,
		string $updateTypeId,
		array $extraData,
		IFederatedUser $federatedUser
	): bool {
		return true;
//		$item = $this->getItem($itemId);
//
//		// TODO: implement SyncedItemLock
//
//		/** @var Entry $entry */
//		$entry = $this->deserialize($this->getArray('addEntry', $extraData), Entry::class);
//		$item->addEntry($entry);
//
//		return $this->serialize($item);
	}

	public function onItemModification(
		string $itemId,
		string $updateType,
		string $updateTypeId,
		array $extraData,
		IFederatedUser $federatedUser
	): void {
		if ($updateType === 'entry' && $this->get('addEntry', $extraData) !== '') {
			/** @var Entry $entry */
			$entry = $this->deserialize($this->getArray('addEntry', $extraData), Entry::class);
			$this->entryRequest->save($entry);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getShareDetails(string $itemId, string $circleId): array {
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function syncShare(string $itemId, string $circleId, array $extraData): void {
	}


	/**
	 * @param string $itemId
	 *
	 * @return Item
	 * @throws ItemNotFoundException
	 */
	private function getCompleteItem(string $itemId): Item {
		$item = $this->itemRequest->getItem($itemId);
		$item->setEntries($this->entryRequest->getRelated($itemId));

		return $item;
	}
}
