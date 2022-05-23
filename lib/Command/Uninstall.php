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
use OCA\TFS\Db\ItemRequest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Uninstall
 *
 * @package OCA\TFS\Command
 */
class Uninstall extends Base {


	/** @var ItemRequest */
	private $itemRequest;


	/**
	 * Uninstall constructor.
	 *
	 * @param ItemRequest $itemRequest
	 */
	public function __construct(ItemRequest $itemRequest) {
		parent::__construct();

		$this->itemRequest = $itemRequest;
	}


	/**
	 *
	 */
	protected function configure() {
		parent::configure();
		$this->setName('tfs:uninstall')
			 ->setDescription('Uninstall the app and its data');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->itemRequest->uninstall();

		return 0;
	}

}

