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
	 * @return ActiveRow
	 * 
	 * @throws \DatabaseException
	 */
	public function createOne($data)
	{
		$result = $this->database->table($this->table)->insert($data);
		
		if (FALSE === $result) {
			throw new \DatabaseException("Insert on {$this->table} failed! Unable to insert data: " . var_export($data, TRUE));
		}
		
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
     * @throws DatabaseException
     * @return integer number of rows returned
     */
	public function updateOne($id, array $data)
	{
		$result = $this->database->table($this->table)->get($id)->update($data);
		
		if (FALSE === $result) {
			throw new \DatabaseException("Update on {$this->table} failed! Unable to update row wit id:{$id} using data: " . var_export($data, TRUE));
		}
		
		return $result;
	}

    /**
     * Delete row with $id from the table
     *
     * @param mixed $id
     *
     * @throws DatabaseException
     * @return integer number of rows returned
     */
	public function deleteOne($id)
	{
		$result = $this->database->table($this->table)->get($id)->delete();
		
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to delete from {$this->table} row with id:{$id}");
		}
		
		return $result;
	}
}