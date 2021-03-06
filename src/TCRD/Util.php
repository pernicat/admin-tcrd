<?php
namespace TCRD;

class Util
{
	/**
	 * taken from:
	 * http://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
	 * 
	 * @param number $length
	 * @return string
	 */
	static function randomPassword($length = 8) {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < $length; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
	
	/**
	 *
	 * @param string $email
	 * @return string
	 */
	static public function usernameFromEmail($email)
	{
		$parts = explode('@', $email);
		return $parts[0];
	}
	
	/**
	 *
	 * @param string $email
	 * @return string
	 */
	static public function domainFromEmail($email)
	{
		return substr(strrchr($email, '@'), 1);
	}
	
	/**
	 * 
	 * @param string $string
	 * @param string $delimiter
	 * @return array
	 */
	static public function csvToArray($string, $delimiter = ',')
	{
		$values = explode($delimiter, $string);
		return array_map('trim', $values);
	}
}