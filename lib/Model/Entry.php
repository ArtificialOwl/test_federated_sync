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


use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use OCA\TFS\Tools\Db\IQueryRow;
use OCA\TFS\Tools\IDeserializable;
use OCA\TFS\Tools\Traits\TArrayTools;


/**
 * Class Entry
 *
 * @package OCA\TFS\Model
 */
class Entry implements IQueryRow, JsonSerializable, IDeserializable {

	use TArrayTools;


	private int $id = 0;
	private string $uniqueId = '';
	private string $itemId = '';
	private string $title = '';


	public function __construct() {
	}


	/**
	 * @param int $id
	 *
	 * @return Entry
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
	 * @param string $uniqueId
	 *
	 * @return Entry
	 */
	public function setUniqueId(string $uniqueId): self {
		$this->uniqueId = $uniqueId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUniqueId(): string {
		return $this->uniqueId;
	}


	/**
	 * @param string $itemId
	 *
	 * @return Entry
	 */
	public function setItemId(string $itemId): self {
		$this->itemId = $itemId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getItemId(): string {
		return $this->itemId;
	}


	/**
	 * @param string $title
	 *
	 * @return Entry
	 */
	public function setTitle(string $title): self {
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}


	/**
	 * @param array $data
	 *
	 * @return IDeserializable
	 */
	public function import(array $data): IDeserializable {
		$this->setId($this->getInt('id', $data));
		$this->setUniqueId($this->get('uniqueId', $data));
		$this->setItemId($this->get('itemId', $data));
		$this->setTitle($this->get('title', $data));

		return $this;
	}

	/**
	 * @param array $data
	 *
	 * @return IQueryRow
	 */
	public function importFromDatabase(array $data): IQueryRow {
		$this->setId($this->getInt('id', $data));
		$this->setUniqueId($this->get('unique_id', $data));
		$this->setItemId($this->get('item_id', $data));
		$this->setTitle($this->get('title', $data));

		return $this;
	}


	/**
	 * @return array
	 */
	#[Pure]
	#[ArrayShape([
		'id' => 'int',
		'uniqueId' => 'string',
		'itemId' => 'string',
		'title' => 'string'
	])]
	public function jsonSerialize(): array {
		return [
			'id' => $this->getId(),
			'uniqueId' => $this->getUniqueId(),
			'itemId' => $this->getItemId(),
			'title' => $this->getTitle()
		];
	}
}
