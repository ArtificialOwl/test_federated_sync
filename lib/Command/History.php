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


use OCA\TFS\Tools\Exceptions\RowNotFoundException;
use OC\Core\Command\Base;
use OCA\TFS\Db\HistoryRequest;
use OCA\TFS\Model\History as HistoryModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class History
 *
 * @package OCA\TFS\Command
 */
class History extends Base {


	/** @var HistoryRequest */
	private $historyRequest;


	/** @var OutputInterface */
	private $output;


	/**
	 * History constructor.
	 *
	 * @param HistoryRequest $historyRequest
	 */
	public function __construct(HistoryRequest $historyRequest) {
		parent::__construct();

		$this->historyRequest = $historyRequest;
	}


	/**
	 *
	 */
	protected function configure() {
		parent::configure();
		$this->setName('tfs:history')
			 ->setDescription('Display events history')
			 ->addOption('live', '', InputOption::VALUE_NONE, 'Live history')
			 ->addOption('clean', '', InputOption::VALUE_NONE, 'Clean history');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->output = $output;

		if ($input->getOption('clean')) {
			$this->historyRequest->empty();

			return 0;
		}

		if ($input->getOption('live')) {
			$this->live();

			return 0;
		}

		$listing = $this->historyRequest->getHistory();
		foreach ($listing as $history) {
			$this->displayHistory($history);
		}

		return 0;
	}

	/**
	 *
	 */
	private function live(): void {
		stream_set_blocking(STDIN, false);
		readline_callback_handler_install(
			'', function() {
		}
		);

		$this->output->writeln('<comment>Live mode enabled, press \'q\' to quit.</comment>');
		try {
			$last = $this->historyRequest->getLastEntry();
			$lastHistory = $last->getId();
		} catch (RowNotFoundException $e) {
			$lastHistory = 0;
		}

		while (true) {
			$entries = $this->historyRequest->getLastEntries($lastHistory);
			foreach ($entries as $history) {
				$this->displayHistory($history);
				$lastHistory = $history->getId();
			}

			sleep(1);
			// catching 'q' to leave loop.
			$k = fread(STDIN, 9999);
			if ($k !== '') {
				$k = substr($k, 0, 1);
				if (strtolower($k) === 'q') {
					break;
				}
			}
		}
	}


	/**
	 * @param HistoryModel $history
	 */
	private function displayHistory(HistoryModel $history): void {
		$this->output->writeln('- ' . $history->getLine());
	}


}

