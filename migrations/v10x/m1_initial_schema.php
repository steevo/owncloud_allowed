<?php
/**
*
* Owncloud Allowed extension for the phpBB Forum Software package.
*
* @copyright (c) 2016 Steevo <http://www.steevo.fr>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace steevobb\owncloud_allowed\migrations\v10x;

/**
* Migration stage 1: Initial schema changes to the database
*/
class m1_initial_schema extends \phpbb\db\migration\migration
{
	/**
	* Assign migration file dependencies for this migration
	*
	* @return array Array of migration files
	* @static
	* @access public
	*/
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	* Add the ownclound column to the users table.
	*
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_owncloud'	=> array('BOOL', 0),
				),
			),
		);
	}

	/**
	* Drop the owncloud column from the users table.
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_owncloud',
				),
			),
		);
	}
}
