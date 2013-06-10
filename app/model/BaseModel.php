<?php
class BaseModel
{
	/**
	 *
	 * @var \Nette\Database\Connection 
	 */
	protected $database;
	
	/**
	 *
	 * @var \Logger\ILogger 
	 */
	protected $logger;
	
	public function __construct(\Nette\Database\Connection $database, \Logger\ILogger $logger)
	{		
		$this->database = $database;
		$this->logger = $logger;
	}
}