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
 * Class ShareRequestBuilder
 *
 * @package OCA\TFS\Db
 */
class ShareRequestBuilder extends CoreQueryBuilder {


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getShareInsertSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_SHARES);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getShareUpdateSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_SHARES);

		return $qb;
	}


	/**
	 * @return CoreRequestBuilder
	 */
	protected function getShareSelectSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->select('s.id', 's.item_id', 's.circle_id')
		   ->from(self::TABLE_SHARES, 's')
		   ->setDefaultSelectAlias('s');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return CoreRequestBuilder
	 */
	protected function getShareDeleteSql(): CoreRequestBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_SHARES);

		return $qb;
	}


	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Share
	 * @throws RowNotFoundException
	 */
	public function getItemFromRequest(CoreRequestBuilder $qb): Share {
		/** @var Share $share */
		$share = $qb->asItem(Share::class);

		return $share;
	}

	/**
	 * @param CoreRequestBuilder $qb
	 *
	 * @return Share[]
	 */
	public function getItemsFromRequest(CoreRequestBuilder $qb): array {
		return $qb->asItems(Share::class);
	}

}

