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


namespace OCA\TFS\Model;


use OCA\TFS\Tools\Db\IQueryRow;
use OCA\TFS\Tools\Traits\TArrayTools;


/**
 * Class History
 *
 * @package OCA\TFS\Model
 */
class History implements IQueryRow {

	use TArrayTools;

	private int $id = 0;
	private string $line = '';


	/**
	 * History constructor.
	 */
	public function __construct() {
	}


	/**
	 * @param int $id
	 *
	 * @return History
	 */
	public function setId(int $id): self {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}


	/**
	 * @param string $line
	 *
	 * @return History
	 */
	public function setLine(string $line): self {
		$this->line = $line;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLine(): string {
		return $this->line;
	}


	/**
	 * @param array $data
	 *
	 * @return IQueryRow
	 */
	public function importFromDatabase(array $data): IQueryRow {
		$this->setId($this->getInt('id', $data));
		$this->setLine($this->get('line', $data));

		return $this;
	}
}
