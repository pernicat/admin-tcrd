<?php
namespace TCRD\Wrapper;

class UserWrapper extends ModelWrapper
{
	public function __construct(\Google_Service_Directory_User $object, $config = array())
	{
		parent::__construct($object, $config);
	}
}