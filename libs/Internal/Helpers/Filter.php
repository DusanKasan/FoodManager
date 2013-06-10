<?php

namespace Internal\Helpers;

use \Nette\Utils\Arrays;

/**
 * Description of Filter
 *
 * @author Dusan
 */
abstract class Filter
{
	/**
	 * Original array
	 * 
	 * @var array 
	 */
	protected $original_data;
	
	/**
	 * Transformed array
	 * 
	 * @var array 
	 */
	protected $transformed_data;
	
	/**
	 * Signal what to do with unknown fields
	 * 
	 * @var boolean
	 */
	protected $parse_unknown_fields = FALSE;
	
	public function __construct(array $data = array())
	{
		$this->original_data = $data;
		$this->transform();
	}
	
	/**
	 * Magic getter for $this->transformed_data access
	 * 
	 * @param string $key key in transformed array
	 * 
	 * @return mixed 
	 */
	public function __get($key)
	{ 
		return isset($this->transformed_data[$key]) ? $this->transformed_data[$key] : NULL;
	}
	
	/**
	 * Magic setter.
	 * Sets the value in both transformed and original data.
	 * 
	 * @param string $key key in transformed array
	 * @param mixed $value value to be set
	 */
	public function __set($key, $value)
	{
		$original_key = $this->getOriginalKey($key);
		
		$this->original_data[$original_key] = $value;
		$this->transformed_data[$key] = $value;
	}
	
	/**
	 * Tell the parser (not) to parse unknow fields into transformed array 
	 * 
	 * @param boolean $parse_unknown_fields
	 * 
	 * @return \Internal\Helpers\Filter 
	 */
	public function setStrict($parse_unknown_fields = TRUE)
	{
		$this->parse_unknown_fields = $parse_unknown_fields;
		
		return $this; //fluid
	}
	
	/**
	 * Get the original data
	 * 
	 * @return array 
	 */
	public function getOriginalData()
	{
		return $this->original_data;
	}
	
	/**
	 * Get data from transformed_data using $key and $default_value.
	 * 
	 * @param string $key key from transformed_data
	 * @param mixed $default_value
	 * 
	 * @return mixed 
	 */
	public function get($key, $default_value = NULL)
	{
		return Arrays::get($this->transformed_data, $key, $default_value);
	}
	
	/**
	 * Fetch value for $key, using $default_value if it doesn't exists,
	 * then unset it from filter. 
	 * 
	 * If $unset_original is set to TRUE, unset this value's representation in original data.
	 * 
	 * @param type $key
	 * @param type $default_value
	 * @param type $unset_original
	 * @return type 
	 */
	public function pop($key, $default_value = NULL, $unset_original = TRUE)
	{
		$value = $this->get($key, $default_value);
		unset($this->transformed_data[$key]);
		
		if ($unset_original) {
			$original_key = $this->getOriginalKey($key);
			unset($this->original_data[$original_key]);
		}
		
		return $value;
	}
	
	/**
	 * Transform the data and return this
	 * 
	 * @return \Internal\Helpers\Filter 
	 */
	public function transform()
	{
		$this->transformed_data = $this->build();
		
		return $this; //fluid
	}
	
	/**
	 * Transform the data and return it
	 * 
	 * @return array 
	 */
	public function export()
	{
		$this->transform();
		
		return $this->transformed_data;
	}
	
	/**
	 * Sets default value. Beware of using this on variables that can be NULL,
	 * because of limitations enposed by isset(). It can not differentiate 
	 * between NULL variable and undefined one!
	 * 
	 * @param string $original_key Key from the original array
	 * @param string $default_value 
	 */
	public function setDefault($original_key, $default_value)
	{
		if (!isset($this->original_data[$original_key])) {
			$this->original_data[$original_key] = $default_value;
		}
	}
	
	/**
	 * Execute the parsing
	 * 
	 * @param array $data $this->original_data
	 * 
	 * @return array 
	 */
	protected function build()
	{
		$parse_info = $this->provideParseInfo();
		$transformed_data = array();
		
		foreach ($this->original_data as $key => $value) {
			if (in_array($key, array_flip($parse_info))) {
				$transformed_data[$parse_info[$key]] = $value;
			} elseif ($this->parse_unknown_fields) {
				$transformed_data[$key] = $value;
			}
		}
		
		return $transformed_data;
	}
	
	/**
	 * Get original key for transformed key
	 * 
	 * @param string $key Transformad key
	 * 
	 * @return string 
	 */
	protected function getOriginalKey($key)
	{
		$flipped = array_flip($this->provideParseInfo());
		return isset($flipped[$key]) ? $flipped[$key] : NULL;
	}
	
	/**
	 * Export only variables with keys specified in input 
	 * 
	 * @param array Array of keys to export
	 * 
	 * @return array
	 */
	protected function exportSubset(array $keys_to_export)
	{	
		return array_intersect_key($this->export(), array_combine($keys_to_export, $keys_to_export));
	}

	//This method should return assoc array structured as:
	//[old_key] => [new_key]
	abstract protected function provideParseInfo();
}
