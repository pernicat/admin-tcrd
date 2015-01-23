<?php
namespace TCRD\File;

class FileAccess
{
	/**
	 * 
	 * @var string
	 */
	protected $file;
	
	/**
	 * 
	 * @param string $file
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}
	
	/**
	 *
	 * @throws Exception
	 * @return string
	 */
	public function load()
	{
		if (!$this->file) {
			throw new \Exception("'{$this->file}' has not been set");
		}
	
		if (!is_readable($this->file)) {
			throw new Exception(
					"Could not load access token from " .
					"'{$this->file}'");
		}
		return file_get_contents($location);
	}
	
	/**
	 *
	 * @param string $token        	
	 * @throws \Exception
	 * @return \TCRD\Wrapper\ClientWrapper
	 */
	public function save($token) 
	{
		if (!$this->file) {
			throw new \Exception("'{$this->file}' has not been set");
		}
		
		$dirname = dirname($this->file);
		
		// Create the directory if neccesary
		if (!realpath($dirname)) {
			if (! mkdir($dirname, 0660, true)) {
				throw new \Exception("Could not create directory '$dirname'");
			}
		}
		
		if (!file_put_contents($this->file, $token)) {
			throw new \Exception("could not write file '{$this->file}'");
		}
		return $this;
	}
}