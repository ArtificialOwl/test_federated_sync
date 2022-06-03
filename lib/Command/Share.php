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


use Exception;
use OC\Core\Command\Base;
use OCA\Circles\CirclesManager;
use OCA\Circles\Exceptions\FederatedUserException;
use OCA\Circles\Exceptions\FederatedUserNotFoundException;
use OCA\Circles\Exceptions\InvalidIdException;
use OCA\Circles\Exceptions\RequestBuilderException;
use OCA\Circles\Exceptions\SingleCircleNotFoundException;
use OCA\TFS\AppInfo\Application;
use OCA\TFS\Db\ItemRequest;
use OCA\TFS\FederatedItems\TestFederatedSync;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Show
 *
 * @package OCA\TFS\Command
 */
class Share extends Base {


	private ItemRequest $itemRequest;


	public function __construct(ItemRequest $itemRequest) {
		parent::__construct();
		$this->itemRequest = $itemRequest;
	}


	protected function configure() {
		parent::configure();
		$this->setName('tfs:share')
			 ->setDescription('Share top-level data to a Circle')
			 ->addArgument('item_id', InputArgument::REQUIRED, 'ItemId to share')
			 ->addArgument('circle_id', InputArgument::REQUIRED, 'CircleId to share the item to')
			 ->addArgument('initiator', InputArgument::REQUIRED, 'set an initiator to the request')
			 ->addOption('source', '', InputOption::VALUE_REQUIRED, 'Source in case of re-share', '');
	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 * @throws FederatedUserException
	 * @throws FederatedUserNotFoundException
	 * @throws InvalidIdException
	 * @throws RequestBuilderException
	 * @throws SingleCircleNotFoundException
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 * @throws \OCA\TFS\Exceptions\ItemNotFoundException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$itemId = $input->getArgument('item_id');
		$circleId = $input->getArgument('circle_id');
		$singleId = $input->getArgument('initiator');
		$source = $input->getOption('source');

		/** @var CirclesManager $circleManager */
		$circleManager = \OC::$server->get(CirclesManager::class);

		try {
			$initiator = $circleManager->getFederatedUser($singleId);
		} catch (Exception $e) {
			try {
				$initiator = $circleManager->getLocalFederatedUser($singleId);
			} catch (Exception $e) {
				throw new Exception('initiator userId/singleId not found');
			}
		}

		$this->itemRequest->getItem($itemId);

		$circleManager->startSession($initiator);
		$circleManager->getShareManager(Application::APP_ID, TestFederatedSync::ITEM_TYPE)
					  ->createShare($itemId, $circleId, ['test' => 42]);

		return 0;
	}
}
