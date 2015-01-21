<?php
namespace TCRD\Wrapper;

class ModelWrapper implements \ArrayAccess
{
	/**
	 * 
	 * @var \Google_Model
	 */
	protected $model;
	
	/**
	 * 
	 * @param \Google_Model $model
	 */
	public function __construct(\Google_Model $model)
	{
		$this->model = $model;
	}
	
	/**
	 * 
	 * @return Google_Model
	 */
	public function getModel()
	{
		return $this->model;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		return $this->model->offsetExists($offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		return $this->model->offsetGet($offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->model->offsetSet($offset, $value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		$this->model->offsetUnset($offset);
	}
}