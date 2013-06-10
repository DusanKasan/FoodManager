<?php
class BaseModel
{
	/**
	 *
	 * @var \Nette\Database\Connection 
	 */
	protected $database;

	public function __construct(\Nette\Database\Connection $database)
	{		
		$this->database = $database;
	}
}