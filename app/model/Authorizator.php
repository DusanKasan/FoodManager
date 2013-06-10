<?php
/**
 * 
 */
class Authorizator extends \Nette\Object implements \Nette\Security\IAuthorizator
{
	const CREATE = 'create';
	const VIEW = 'view';
	const EDIT = 'edit';
	const REMOVE = 'remove';
	
	/** @var Nette\Database\Connection */
	private $database;

	private $authorization_array = array();
	
	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
		
		foreach ($this->database->table('roles_resources_privileges') as $row) {
			$this->authorization_array[$row->role->role][$row->resource][$row->privilege] = TRUE;
		}
	}
	
    function isAllowed($role, $resource, $privilege = self::VIEW)
    {
		return (isset($this->authorization_array[$role][$resource][$privilege])) ? TRUE : FALSE;
    }

}
