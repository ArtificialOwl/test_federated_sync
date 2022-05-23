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


use OCA\Circles\IFederatedSyncManager;
use OCA\Circles\Model\FederatedUser;
use OCA\Circles\Model\Membership;
use OCA\TFS\AppInfo\Application;
use OCA\TFS\Db\EntryRequest;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\Db\ShareRequest;
use OCA\TFS\Exceptions\ItemNotFoundException;
use OCA\TFS\Model\Item;
use OCA\TFS\Model\Share;
use OCA\TFS\Tools\Traits\TArrayTools;
use OCA\TFS\Tools\Traits\TDeserialize;


class TestFederatedSync implements IFederatedSyncManager {
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
	 * @param string $itemId
	 * @param string $circleId
	 *
	 * @return bool
	 */
	public function isSharedWithCircle(string $itemId, string $circleId): bool {
		return true;
//		$item = $this->shareRequest->getShare($itemId, $circleId);
	}


	/**
	 * @param string $itemId
	 *
	 * @return array
	 * @throws ItemNotFoundException
	 */
	public function serializeItem(string $itemId): array {
		$item = $this->itemRequest->getItem($itemId);
		$item->setEntries($this->entryRequest->getRelated($itemId));

		return $this->serialize($item);
	}


	/**
	 * @param string $itemId
	 * @param array $serializedData
	 *
	 * @throws \OCA\Circles\Tools\Exceptions\InvalidItemException
	 */
	public function syncItem(string $itemId, array $serializedData): void {
		/** @var Item $item */
		$item = $this->deserialize($serializedData, Item::class);

		$this->itemRequest->save($item);

		$this->entryRequest->removeEntriesFromItem($item->getUniqueId());
		$this->entryRequest->saveAll($item->getEntries());
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param array $extraData
	 * @param FederatedUser $federatedUser
	 *
	 * @return bool
	 */
	public function isShareCreatable(
		string $itemId,
		string $circleId,
		array $extraData,
		FederatedUser $federatedUser
	): bool {
//		echo '___' . json_encode($federatedUser, JSON_PRETTY_PRINT) . "\n";

		return true;
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param array $extraData
	 * @param FederatedUser $federatedUser
	 */
	public function onShareCreation(
		string $itemId,
		string $circleId,
		array $extraData,
		FederatedUser $federatedUser
	): void {
		$share = new Share();
		$share->setItemId($itemId)
			  ->setCircleId($circleId)
			  ->setPermissions($this->getInt('permission', $extraData));

		$this->shareRequest->save($share);
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param array $extraData
	 * @param Membership $membership
	 *
	 * @return bool
	 */
	public function isShareModifiable(
		string $itemId,
		string $circleId,
		array $extraData,
		Membership $membership
	): bool {
		return true;
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param array $extraData
	 * @param Membership $membership
	 */
	public function onShareModification(
		string $itemId,
		string $circleId,
		array $extraData,
		Membership $membership
	): void {
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param Membership $membership
	 *
	 * @return bool
	 */
	public function isShareDeletable(
		string $itemId,
		string $circleId,
		Membership $membership
	): bool {
		return true;
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param Membership $membership
	 */
	public function onShareDeletion(string $itemId, string $circleId, Membership $membership): void {
	}

	public function isItemUpdatable(
		string $itemId,
		array $extraData,
		FederatedUser $federatedUser
	): bool {
		return true;
	}


	public function getShareDetails(string $itemId, string $circleId): array {
		return [];
	}

	public function syncShare(string $itemId, string $circleId, array $extraData): void {
	}
}
