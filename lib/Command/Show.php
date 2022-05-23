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

namespace OCA\TFS\Command;

use OC\Core\Command\Base;
use OCA\TFS\Db\EntryRequest;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\Model\Entry;
use OCA\TFS\Model\Item;
use OCA\TFS\Service\ItemService;
use OCA\TFS\Tools\Exceptions\InvalidItemException;
use OCA\TFS\Tools\Exceptions\ItemNotFoundException;
use OCA\TFS\Tools\Exceptions\UnknownTypeException;
use OCA\TFS\Tools\Model\SimpleDataStore;
use OCA\TFS\Tools\Model\TreeNode;
use OCA\TFS\Tools\Traits\TConsoleTree;
use OCP\IUserManager;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Base {

	use TConsoleTree;

	private IUserManager $userManager;
	private ItemRequest $itemRequest;
	private EntryRequest $entryRequest;
	private ItemService $itemService;

	/**
	 * @param IUserManager $userManager
	 * @param ItemRequest $itemRequest
	 * @param EntryRequest $entryRequest
	 * @param ItemService $itemService
	 */
	public function __construct(
		IUserManager $userManager,
		ItemRequest $itemRequest,
		EntryRequest $entryRequest,
		ItemService $itemService
	) {
		parent::__construct();

		$this->userManager = $userManager;
		$this->itemRequest = $itemRequest;
		$this->entryRequest = $entryRequest;
		$this->itemService = $itemService;
	}


	/**
	 *
	 */
	protected function configure() {
		parent::configure();
		$this->setName('tfs:show')
			 ->setDescription('Display available data')
			 ->addArgument('user_id', InputArgument::OPTIONAL, 'display entries limited to userId');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int {
		$userId = $input->getArgument('user_id');

		if ($userId) {
			$user = $this->userManager->get($userId);
			if (is_null($user)) {
				throw new InvalidOptionException('must specify a valid user');
			}

			$items = $this->itemService->getItems($user->getUID());
		} else {
			$items = $this->itemRequest->getItems();
		}

		$tree = new TreeNode(null, new SimpleDataStore());
		foreach ($items as $item) {
			$node = new TreeNode($tree, new SimpleDataStore(['item' => $item]));
			$entries = $this->entryRequest->getForItem($item->getUniqueId());
			foreach ($entries as $entry) {
				new TreeNode($node, new SimpleDataStore(['entry' => $entry]));
			}
		}

		$this->drawTree(
			$tree, [$this, 'displayLeaf'],
			[
				'height' => 1,
				'node-spacing' => 0,
				'item-spacing' => 0,
			]
		);

		return 0;
	}


	/**
	 * @param SimpleDataStore $data
	 *
	 * @return string
	 * @throws InvalidItemException
	 * @throws ItemNotFoundException
	 * @throws UnknownTypeException
	 */
	public function displayLeaf(SimpleDataStore $data): string {
		if ($data->hasKey('item')) {
			/** @var Item $item */
			$item = $data->gObj('item', Item::class);

			return '<info>' . $item->getUniqueId() . '</info> - ' . $item->getTitle();
		}

		if ($data->hasKey('entry')) {
			/** @var Entry $entry */
			$entry = $data->gObj('entry', Entry::class);

			return '<info>' . $entry->getUniqueId() . '</info> - ' . $entry->getTitle();
		}

		return '';
	}

}

