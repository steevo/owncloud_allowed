<?php
/**
*
* Owncloud Allowed extension for the phpBB Forum Software package.
*
* @copyright (c) 2016 Steevo <http://www.steevo.fr>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace steevobb\owncloud_allowed\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/**
	* define our constants
	**/
	const OWNCLOUD_ALLOWED = 1;
	const OWNCLOUD_NOT_ALLOWED = 0;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\request\request               $request           Request object
	* @param \phpbb\template\template             $template          Template object
	* @param \phpbb\user                          $user              User object
	* @access public
	*/
	public function __construct(\phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_users_modify_profile'				=> 'user_owncloud_profile',
			'core.acp_users_profile_modify_sql_ary'		=> 'user_owncloud_profile_sql',
			'core.acp_users_profile_validate'			=> 'user_owncloud_profile_validate',
		);
	}

	/**
	* Allow change owncloud option
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_owncloud_profile($event)
	{
		$user_owncloud = $event['user_row']['user_owncloud'];

		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'user_owncloud'	=> $this->request->variable('owncloud', $user_owncloud),
		));

		$this->user->add_lang_ext('steevobb/owncloud_allowed', 'owncloud');

		$this->template->assign_vars(array(
			'OWNCLOUD_ALLOWED'			=> self::OWNCLOUD_ALLOWED,
			'OWNCLOUD_NOT_ALLOWED'		=> self::OWNCLOUD_NOT_ALLOWED,

			'S_OWNCLOUD_ALLOWED'	=> ($event['data']['user_owncloud'] == self::OWNCLOUD_ALLOWED) ? true : false,
			'S_OWNCLOUD_NOT_ALLOWED'	=> ($event['data']['user_owncloud'] == self::OWNCLOUD_NOT_ALLOWED) ? true : false,
		));
	}

	/**
	* Validate owncloud value
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_owncloud_profile_validate($event)
	{
			$array = $event['error'];
			//ensure owncloud is validated
			if (!function_exists('validate_data'))
			{
				include($this->root_path . 'includes/functions_user.' . $this->php_ext);
			}
			$validate_array = array(
				'user_owncloud'	=> array('num', true, 0, 1),
			);
			$error = validate_data($event['data'], $validate_array);
			$event['error'] = array_merge($array, $error);
	}

	/**
	* Update the database
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function user_owncloud_profile_sql($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
				'user_owncloud' => $event['data']['user_owncloud'],
		));
	}
}
