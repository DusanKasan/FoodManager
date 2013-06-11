<?php
/**
 * Base model
 * 
 * @author Dusan Kasan <dusan@kasan.sk>
 */
abstract class BaseModel
{
	/**
	 * @var \Nette\Database\Connection 
	 */
	protected $database;
	
	/**
	 * Constructor
	 * 
	 * @param \Nette\Database\Connection $database 
	 */
	public function __construct(\Nette\Database\Connection $database)
	{
		$this->database = $database;
	}
}