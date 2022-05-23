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


namespace OCA\TFS\Db;


use OCA\TFS\Tools\Exceptions\RowNotFoundException;
use OCA\TFS\Model\Share;


/**
 * Class ShareRequest
 *
 * @package OCA\Circles\Db
 */
class ShareRequest extends ShareRequestBuilder {


	/**
	 * @param Share $share
	 */
	public function save(Share $share): void {
		$qb = $this->getShareInsertSql();

		$qb->setValue('item_id', $qb->createNamedParameter($share->getItemId()))
		   ->setValue('circle_id', $qb->createNamedParameter($share->getCircleId()));

		$qb->execute();
	}


	/**
	 * @param string $itemId
	 * @param string $circleId
	 *
	 * @return Share
	 * @throws RowNotFoundException
	 */
	public function searchShare(string $itemId, string $circleId): Share {
		$qb = $this->getShareSelectSql();
		$qb->limitToItemId($itemId);
		$qb->limitToCircleId($circleId);

		return $this->getItemFromRequest($qb);
	}

}

