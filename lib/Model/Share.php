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
use OCA\TFS\Tools\Traits\TArrayTools;


/**
 * Class Share
 *
 * @package OCA\TFS\Model
 */
class Share implements IQueryRow, JsonSerializable {

	use TArrayTools;

	public const PERMISSION_READ = 1;
	public const PERMISSION_WRITE = 2;
	public const PERMISSION_SHARE = 4;

	private int $id = 0;
	private string $itemId = '';
	private string $circleId = '';
	private int $permissions = 0;


	/**
	 * Share constructor.
	 */
	public function __construct() {
	}


	/**
	 * @param int $id
	 *
	 * @return Share
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
	 * @param string $itemId
	 *
	 * @return Share
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
	 * @param string $circleId
	 *
	 * @return Share
	 */
	public function setCircleId(string $circleId): self {
		$this->circleId = $circleId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCircleId(): string {
		return $this->circleId;
	}


	/**
	 * @param int $permissions
	 *
	 * @return Share
	 */
	public function setPermissions(int $permissions): self {
		$this->permissions = $permissions;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPermissions(): int {
		return $this->permissions;
	}

	/**
	 * @param int $action
	 *
	 * @return bool
	 */
	public function isAllowed(int $action): bool {
		return (($this->getPermissions() & $action) !== 0);
	}


	/**
	 * @param array $data
	 *
	 * @return IQueryRow
	 */
	public function importFromDatabase(array $data): IQueryRow {
		$this->setId($this->getInt('id', $data));
		$this->setPermissions($this->getInt('permissions', $data));
		$this->setItemId($this->get('item_id', $data));
		$this->setCircleId($this->get('circle_id', $data));

		return $this;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'id' => $this->getId(),
			'itemId' => $this->getItemId(),
			'circleId' => $this->getCircleId(),
		];
	}
}
