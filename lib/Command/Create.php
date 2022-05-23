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
use OCA\Circles\CirclesManager;
use OCA\TFS\Db\EntryRequest;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\Exceptions\ItemNotFoundException;
use OCA\TFS\Model\Entry;
use OCA\TFS\Model\Item;
use OCA\TFS\Tools\Exceptions\RowNotFoundException;
use OCA\TFS\Tools\Traits\TStringTools;
use OCP\IUserManager;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Create
 *
 * @package OCA\TFS\Command
 */
class Create extends Base {

	use TStringTools;

	private IUserManager $userManager;
	private ItemRequest $itemRequest;
	private EntryRequest $entryRequest;
	private OutputInterface $output;


	/**
	 * Create constructor.
	 *
	 * @param IUserManager $userManager
	 * @param ItemRequest $itemRequest
	 * @param EntryRequest $entryRequest
	 */
	public function __construct(
		IUserManager $userManager,
		ItemRequest $itemRequest,
		EntryRequest $entryRequest
	) {
		parent::__construct();

		$this->userManager = $userManager;
		$this->itemRequest = $itemRequest;
		$this->entryRequest = $entryRequest;
	}


	/**
	 *
	 */
	protected function configure() {
		parent::configure();
		$this->setName('tfs:create')
			 ->setDescription('Create random data at top level, or related to a root item')
			 ->addOption(
				 'related', '', InputOption::VALUE_REQUIRED, 'create random data, related to a root item'
			 )
			 ->addOption(
				 'user', '', InputOption::VALUE_REQUIRED, 'assign item to a UserId'
			 );

	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 * @throws RowNotFoundException
	 * @throws ItemNotFoundException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$related = $input->getOption('related');
		$userId = $input->getOption('user');

		if ($userId) {
			$user = $this->userManager->get($userId);
			if (is_null($user)) {
				throw new InvalidOptionException('must specify a valid user');
			}
			try {
				$item = $this->createItem($user->getUID());
			} catch (ItemNotFoundException $e) {
				throw new ItemNotFoundException('item creation failed');
			}
			$output->writeln(json_encode($item, JSON_PRETTY_PRINT));

			return 0;
		}

		if ($related) {
			$entry = $this->createEntry($related);
			$output->writeln(json_encode($entry, JSON_PRETTY_PRINT));

			return 0;
		}

		throw new InvalidOptionException('must specify --user <userId> or --related <itemId>');
	}


	/**
	 * @param string $userId
	 *
	 * @return Item
	 * @throws ItemNotFoundException
	 */
	private function createItem(string $userId): Item {

		/** @var CirclesManager $circlesManager */
		$circlesManager = \OC::$server->get(CirclesManager::class);
		$federatedUser = $circlesManager->getLocalFederatedUser($userId);

		$item = new Item();
		$item->setUserId($userId);
		$item->setUniqueId($this->token(15));
		$item->setTitle($this->generateRandomSentence(rand(3, 9)));
		$item->setUserSingleId($federatedUser->getSingleId());

		$this->itemRequest->save($item);

		return $this->itemRequest->getItem($item->getUniqueId());
	}


	/**
	 * @param string $itemId
	 *
	 * @return Entry
	 * @throws ItemNotFoundException
	 * @throws RowNotFoundException
	 */
	private function createEntry(string $itemId): Entry {
		$this->itemRequest->getItem($itemId);

		$entry = new Entry();
		$entry->setItemId($itemId);
		$entry->setUniqueId($this->token(15));
		$entry->setTitle($this->generateRandomSentence(rand(3, 9)));

		$this->entryRequest->save($entry);

		return $this->entryRequest->getEntry($entry->getUniqueId());
	}

}

