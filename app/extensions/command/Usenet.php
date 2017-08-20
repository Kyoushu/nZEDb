<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program (see LICENSE.txt in the base directory.  If
 * not, see:
 *
 * @link      <http://www.gnu.org/licenses/>.
 * @author    niel
 * @copyright 2017 nZEDb
 */

namespace app\extensions\command;

use nzedb\db\DB;
use nzedb\NNTP;


/**
 * Lets you manually fetch data from your U.S.P.
 *
 *
 *
 * @package app\extensions\command
 */
class Usenet extends \app\extensions\console\Command
{
	public $group = 'alt.binaries.moovee';

	public $msgid = '<0c-A40009D167$y23340x3Gi4781d@I40$O3b586991.Y1al3kC5>';

	/**
	 * Constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		$defaults = [
			'classes'  => $this->_classes,
			'request'  => null,
			'response' => [],
		];
		parent::__construct($config + $defaults);
	}

	public function fetch()
	{
		if (empty($this->msgid)) {
			$this->error("{:red}No message-id (--msgid=) supplied.{:end}");
			$this->msgid = $this->in("message-id?");
		}

		if (empty($this->group)) {
			$this->error("{:red}No group name (--group=) supplied.{:end}");
			$this->group = $this->in("group name?");
		}

		$this->out("{:cyan}Using message-id: '{$this->msgid}'\nGroup: {$this->group}{:end}");

		$nntp = new NNTP(['Settings' => new DB()]);

		$result = $nntp->doConnect();

		if ($result === true) {
			$this->out("{:green}Connected to USP.{:end}");
			$result = $nntp->get_Header($this->msgid, $this->group);

			if ($nntp->isError($result) === false) {
				$this->out("{:green}Fetched headers{:end}");
			} else {
				$this->error("{$result->getMessage()}");
				$this->out("{:green}Damnit an error!!{:end}");
			}
		} else {
			$this->error("{$result->getMessage()}");
			$this->out("{:green}Damnit an error!!{:end}");
		}
	}

	public function run()
	{
		if (!$this->request->args()) {
			return $this->_help();
		}

		return false;
	}

}
