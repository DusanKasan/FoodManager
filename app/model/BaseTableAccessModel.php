<?php
/**
 * Basic CRUD interface to $this->table
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
abstract class BaseTableAccessModel extends BaseModel
{
	/**
	 * @var string 
	 */
	protected $table;
	
	/**
	 * Create row in table using $data
	 * 
	 * @param array $data
	 * 
	 * @return ActiveRow or FALSE on error
	 */
	public function createOne($data)
	{
		$result = $this->database->table($this->table)->insert($data);
		
		$this->logger->log("Created new row in {$this->table} table with", $data, "resulting in", $result->toArray());
		
		return $result;
	}
	
	/**
	 * Get one row from table, specified by $id
	 * 
	 * @param mixed $id
	 * 
	 * @return ActiveRow or FALSE if there is no such row 
	 */
	public function getOne($id)
	{
		return $this->database->table($this->table)->get($id);
	}
	
	/**
	 * Get all rows from table
	 * 
	 * @return Nette\Database\Table\Selection 
	 */
	public function getAll()
	{
		return $this->database->table($this->table);
	}
	
	/**
	 * Get rows from table using $where array to construct where clause
	 * 
	 * @param array $where
	 * 
	 * @return Nette\Database\Table\Selection
	 */
	public function getWhere(array $where)
	{
		return $this->database->table($this->table)->where($where);
	}

	/**
	 * Update row with $id in table using $data
	 *
	 * @param mixed $id
	 * @param array $data
	 * 
	 * @return integer number of rows returned 
	 */
	public function updateOne($id, array $data)
	{
		$result = $this->database->table($this->table)->get($id)->update($data);

		$this->logger->log("Row with id {$id} in {$this->table} table was updated using",
				$data,
				"resulting in",
				$this->getOne($id)->toArray());
		
		return $result;
	}
	
	/**
	 * Delete row with $id from the table
	 * 
	 * @param mixed $id
	 * 
	 * @return integer number of rows returned 
	 */
	public function deleteOne($id)
	{
		
		$row = $this->getOne($id)->toArray();
		$result = $this->database->table($this->table)->get($id)->delete();
		$this->logger->log("Row with id {$id} in {$this->table} table was deleted. The row was ", $row);
		
		return $result;
	}
}