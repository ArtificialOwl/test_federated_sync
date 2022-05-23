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


use OCA\TFS\Tools\Exceptions\InvalidItemException;
use OCA\TFS\Tools\Exceptions\RequestNetworkException;
use OCA\TFS\Tools\Exceptions\RowNotFoundException;
use OCA\TFS\Tools\Exceptions\SignatoryException;
use OCA\TFS\Tools\Exceptions\UnknownTypeException;
use OCA\Circles\Exceptions\CircleNotFoundException;
use OCA\Circles\Exceptions\FederatedEventDSyncException;
use OCA\Circles\Exceptions\FederatedEventException;
use OCA\Circles\Exceptions\FederatedItemException;
use OCA\Circles\Exceptions\FederatedShareAlreadyLockedException;
use OCA\Circles\Exceptions\InitiatorNotConfirmedException;
use OCA\Circles\Exceptions\InitiatorNotFoundException;
use OCA\Circles\Exceptions\OwnerNotFoundException;
use OCA\Circles\Exceptions\RemoteNotFoundException;
use OCA\Circles\Exceptions\RemoteResourceNotFoundException;
use OCA\Circles\Exceptions\UnknownRemoteException;
use OCA\Circles\Model\Federated\FederatedEvent;
use OCA\Circles\Service\CircleService;
use OCA\Circles\Service\FederatedEventService;
use OCA\Circles\Service\FederatedShareService;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\Db\ShareRequest;
use OCA\TFS\Exceptions\ShareException;
use OCA\TFS\FederatedItems\ItemShare;


/**
 * Class ShareService
 *
 * @package OCA\TFS\Service
 */
class ShareService {


	/** @var ShareRequest */
	private $shareRequest;

	/** @var ItemRequest */
	private $itemRequest;

	/** @var FederatedShareService */
	private $federatedShareService;

	/** @var FederatedEventService */
	private $federatedEventService;

	/** @var CircleService */
	private $circleService;

	/** @var HistoryService */
	private $historyService;


	/**
	 * ShareService constructor.
	 *
	 * @param ShareRequest $shareRequest
	 * @param ItemRequest $itemRequest
	 * @param FederatedShareService $federatedShareService
	 * @param FederatedEventService $federatedEventService
	 * @param CircleService $circleService
	 * @param HistoryService $historyService
	 */
	public function __construct(
		ShareRequest $shareRequest,
		ItemRequest $itemRequest,
		FederatedShareService $federatedShareService,
		FederatedEventService $federatedEventService,
		CircleService $circleService,
		HistoryService $historyService
	) {
		$this->shareRequest = $shareRequest;
		$this->itemRequest = $itemRequest;
		$this->federatedShareService = $federatedShareService;
		$this->federatedEventService = $federatedEventService;
		$this->circleService = $circleService;
		$this->historyService = $historyService;
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 * @param string $source
	 *
	 * @throws CircleNotFoundException
	 * @throws FederatedEventDSyncException
	 * @throws FederatedEventException
	 * @throws FederatedItemException
	 * @throws FederatedShareAlreadyLockedException
	 * @throws InitiatorNotConfirmedException
	 * @throws InitiatorNotFoundException
	 * @throws OwnerNotFoundException
	 * @throws RemoteNotFoundException
	 * @throws RemoteResourceNotFoundException
	 * @throws RequestNetworkException
	 * @throws RowNotFoundException
	 * @throws ShareException
	 * @throws SignatoryException
	 * @throws UnknownRemoteException
	 * @throws InvalidItemException
	 * @throws UnknownTypeException
	 */
	public function shareItem(string $itemId, string $circleId, string $source = ''): void {
//		// item exists ?
		$this->itemRequest->getItem($itemId);

		// shares already exists ?
		try {
			$this->shareRequest->searchShare($itemId, $circleId);
			throw new ShareException('Share already exist');
		} catch (RowNotFoundException $e) {
		}

		// locking item to that instance
		if ($source === '') {
			// TODO: can it be move into Circles ?
			try {
				$federatedShare = $this->federatedShareService->lockItem($circleId, $itemId);
				$this->historyService->add(
					'locking: Item ' . $itemId . ' (' . $federatedShare->getLockStatus() . ')'
				);
			} catch (FederatedShareAlreadyLockedException $e) {
				$this->historyService->add('Item ' . $itemId . ' already locked: ' . $e->getMessage());
				throw $e;
			}
		}

		$circle = $this->circleService->getCircle($circleId);

		$event = new FederatedEvent(ItemShare::class);
		$event->setCircle($circle)
			  ->setItemId($itemId)
			  ->setItemSource($source);

		$this->federatedEventService->newEvent($event);
	}


}

