<?php
/**
*
* @package Titania
* @copyright (c) 2012 phpBB Customisation Database Team
* @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_TITANIA'))
{
	exit;
}

if (!class_exists('titania_type_base'))
{
	include(TITANIA_ROOT . 'includes/types/base.' . PHP_EXT);
}

define('TITANIA_TYPE_BBCODE', 7);

class titania_type_bbcode extends titania_type_base
{
	/**
	 * The type id
	 *
	 * @var int type id (for custom types not specified in titania to start, please start with 10 in case we add any extra later)
	 */
	public $id = 7;
	
	/**
	 * For the type name
	 *
	 * @var string (any lang key that includes the type should match this value)
	 */
	public $name = 'bbcode';

	/**
	 * Require upload?
	*/
	public $require_upload = false;

	/**
	 * BBCodes are not downloadable, so don't create Composer packages
	*/
	public $create_composer_packages = false;

	/**
	 * For the url slug
	 *
	 * @var string portion to be used in the URL slug
	 */
	public $url = 'bbcode';
	
	public function __construct()
	{
		$this->lang = phpbb::$user->lang['BBCODE'];
		$this->langs = phpbb::$user->lang['BBCODES'];
	}

	public $extra_upload = false;

	// Validation messages (for the PM)
	public $validation_subject = 'BBCODE_VALIDATION';
	public $validation_message_approve = 'BBCODE_VALIDATION_MESSAGE_APPROVE';
	public $validation_message_deny = 'BBCODE_VALIDATION_MESSAGE_DENY';
	public $upload_agreement = 'BBCODE_UPLOAD_AGREEMENT';
	
	/**
	* Check auth level
	*
	* @param string $auth ('view', 'test', 'validate')
	* @return bool
	*/
	public function acl_get($auth)
	{
		switch ($auth)
		{
			// Can submit a bbcode
			case 'submit' :
				return true;
			break;

			// Can view the bbcode queue discussion
			case 'queue_discussion' :
				return phpbb::$auth->acl_get('u_titania_mod_bbcode_queue_discussion');
			break;

			// Can view the bbcode queue
			case 'view' :
				return phpbb::$auth->acl_get('u_titania_mod_bbcode_queue');
			break;

			// Can validate bbcodes in the queue
			case 'validate' :
				return phpbb::$auth->acl_get('u_titania_mod_bbcode_validate');
			break;

			// Can moderate bbcodes
			case 'moderate' :
				return phpbb::$auth->acl_gets(array('u_titania_mod_bbcode_moderate', 'u_titania_mod_contrib_mod'));
			break;
		}

		return false;
	}

	/**
	* Automatically install the type if required
	*
	* For adding type specific permissions, etc.
	*/
	public function auto_install()
	{
		// If you change anything in here, remember to add the reverse to the uninstall() function below!

		if (!isset(phpbb::$config['titania_num_bbcodes']))
		{
			if (!class_exists('umil'))
			{
				include(PHPBB_ROOT_PATH . 'umil/umil.' . PHP_EXT);
			}

			$umil = new umil(true, phpbb::$db);

			// Permissions
			$umil->permission_add(array(
				'u_titania_mod_bbcode_queue_discussion',
				'u_titania_mod_bbcode_queue',
				'u_titania_mod_bbcode_validate',
				'u_titania_mod_bbcode_moderate',
			));

			// bbcode count holder
			$umil->config_add('titania_num_bbcodes', 0, true);
		}
	}

	public function increment_count()
	{
		set_config('titania_num_bbcodes', ++phpbb::$config['titania_num_bbcodes'], true);
	}

	public function decrement_count()
	{
		set_config('titania_num_bbcodes', --phpbb::$config['titania_num_bbcodes'], true);
	}

	public function get_count()
	{
		return phpbb::$config['titania_num_bbcodes'];
	}	
	
	/**
	* Uninstall the type
	*/
	public function uninstall()
	{
		if (isset(phpbb::$config['titania_num_bbcodes']))
		{
			if (!class_exists('umil'))
			{
				include(PHPBB_ROOT_PATH . 'umil/umil.' . PHP_EXT);
			}

			$umil = new umil(true, phpbb::$db);

			// Permissions
			$umil->permission_remove(array(
				'u_titania_mod_bbcode_queue_discussion',
				'u_titania_mod_bbcode_queue',
				'u_titania_mod_bbcode_validate',
				'u_titania_mod_bbcode_moderate',
			));

			// bbcode count holder
			$umil->config_remove('titania_num_bbcodes');
		}
	}
}
