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


use JsonSerializable;
use OCA\TFS\Tools\Db\IQueryRow;
use OCA\TFS\Tools\IDeserializable;
use OCA\TFS\Tools\Traits\TArrayTools;
use OCA\TFS\Tools\Traits\TDeserialize;


class Item implements IQueryRow, JsonSerializable, IDeserializable {
	use TArrayTools;
	use TDeserialize;

	private int $id = 0;
	private string $uniqueId = '';
	private string $title = '';
	private string $userId = '';
	private string $userSingleId = '';

	/** @var Entry[] */
	private array $entries = [];

	public function __construct() {
	}


	/**
	 * @param int $id
	 *
	 * @return Item
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
	 * @return Item
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
	 * @param string $title
	 *
	 * @return Item
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
	 * @param string $userId
	 *
	 * @return Item
	 */
	public function setUserId(string $userId): self {
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string {
		return $this->userId;
	}


	/**
	 * @param string $singleId
	 *
	 * @return Item
	 */
	public function setUserSingleId(string $singleId): self {
		$this->userSingleId = $singleId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserSingleId(): string {
		return $this->userSingleId;
	}

	/**
	 * @param Entry[] $entries
	 *
	 * @return Item
	 */
	public function setEntries(array $entries): self {
		$this->entries = $entries;

		return $this;
	}

	/**
	 * @return Entry[]
	 */
	public function getEntries(): array {
		return $this->entries;
	}


	/**
	 * @param array $data
	 *
	 * @return IQueryRow
	 */
	public function importFromDatabase(array $data): IQueryRow {
		$this->setId($this->getInt('id', $data));
		$this->setUniqueId($this->get('unique_id', $data));
		$this->setUserId($this->get('user_id', $data));
		$this->setUserSingleId($this->get('user_single_id', $data));
		$this->setTitle($this->get('title', $data));

		return $this;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'id' => $this->getId(),
			'uniqueId' => $this->getUniqueId(),
			'userId' => $this->getUserId(),
			'userSingleId' => $this->getUserSingleId(),
			'title' => $this->getTitle(),
			'entries' => $this->getEntries()
		];
	}

	/**
	 * @param array $data
	 *
	 * @return IDeserializable
	 */
	public function import(array $data): IDeserializable {
		$this->setId($this->getInt('id', $data));
		$this->setUniqueId($this->get('uniqueId', $data));
		$this->setUserId($this->get('userId', $data));
		$this->setUserSingleId($this->get('userSingleId', $data));
		$this->setTitle($this->get('title', $data));
		$this->setEntries($this->deserializeArray($this->getArray('entries', $data), Entry::class));

		return $this;
	}

}

