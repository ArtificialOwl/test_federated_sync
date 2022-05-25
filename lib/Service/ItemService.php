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


namespace OCA\TFS\Service;


use OCA\Circles\CirclesManager;
use OCA\TFS\Db\ItemRequest;

class ItemService {

	private ItemRequest $itemRequest;
	private CirclesManager $circlesManager;

	/**
	 * @param ItemRequest $itemRequest
	 */
	public function __construct(ItemRequest $itemRequest) {
		$this->itemRequest = $itemRequest;
//		$this->circlesManager = \OC::$server->get(CirclesManager::class);
	}


	/**
	 * @param string $userId
	 *
	 * @return array
	 */
	public function getItems(string $userId = ''): array {
		if ($userId) {
			return $this->itemRequest->filterItems($userId);
		} else {
			return $this->itemRequest->getItems();
		}
	}


}

