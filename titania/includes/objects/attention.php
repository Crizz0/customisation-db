<?php
/**
*
* @package Titania
* @version $Id$
* @copyright (c) 2008 phpBB Customisation Database Team
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_TITANIA'))
{
	exit;
}

if (!class_exists('titania_database_object'))
{
	require TITANIA_ROOT . 'includes/core/object_database.' . PHP_EXT;
}

/**
 * Class to abstract attention items
 * @package Titania
 */
class titania_attention extends titania_database_object
{
	/**
	 * Database table to be used
	 *
	 * @var string
	 */
	protected $sql_table = TITANIA_ATTENTION_TABLE;

	/**
	 * Primary sql identifier
	 *
	 * @var string
	 */
	protected $sql_id_field = 'attention_id';

	/**
	 * Constructor class for the attention object
	 */
	public function __construct()
	{
		// Configure object properties
		$this->object_config = array_merge($this->object_config, array(
			'attention_id'					=> array('default' => 0),
			'attention_type'				=> array('default' => 0), // attention type constants (reported, needs approval, etc)
			'attention_object_type'			=> array('default' => 0),
			'attention_object_id'			=> array('default' => 0),
			'attention_url'					=> array('default' => ''),
			'attention_requester'			=> array('default' => (int) phpbb::$user->data['user_id']),
			'attention_time'				=> array('default' => titania::$time),
			'attention_close_time'			=> array('default' => 0),
			'attention_close_user'			=> array('default' => 0),
			'attention_title'				=> array('default' => ''),
			'attention_description'			=> array('default' => ''),
		));
	}

	public function submit()
	{
		$this->attention_url = titania_url::unbuild_url($this->attention_url);

		parent::submit();
	}

	/**
	* Close the attention
	*/
	public function close()
	{
		$this->attention_close_time = titania::$time;
		$this->attention_close_user = phpbb::$user->data['user_id'];

		$this->submit();
	}

	/**
	* Get the URL for the item needing attention
	*
	* @return string the built url
	*/
	public function get_url()
	{
		$base = $append = false;
		titania_url::split_base_params($base, $append, $this->attention_url);

		return titania_url::build_url($base, $append);
	}

	/**
	* Assign the details for the attention object
	*
	* @param bool $return True to return the data, false to display it
	*/
	public function assign_details($return = false)
	{
		$output = array(
			'ATTENTION_TYPE'		=> $this->attention_type,
			'ATTENTION_TIME'		=> phpbb::$user->format_date($this->attention_time),
			'ATTENTION_CLOSE_TIME'	=> ($this->attention_close_time) ? phpbb::$user->format_date($this->attention_close_time) : '',
			'ATTENTION_TITLE'		=> $this->attention_title,

			'U_VIEW_ATTENTION'		=> $this->get_url(),
		);

		if ($return)
		{
			return $output;
		}

		phpbb::$template->assign_vars($output);
	}
}
