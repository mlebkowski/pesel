<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-31-01 11:03
 */
namespace Nassau\PESEL;

class PESEL
{
	const PESEL_LENGTH = 11;
	const PESEL_WEIGHTS = '9731973197';

	const GENDER_MALE = 'male';
	const GENDER_FEMALE = 'female';
	const PESEL_GENDER_INDEX = 9;

	protected $number;

	public function __construct($number)
	{
		$number = trim($number);
		if (false === self::isValid($number))
		{
			throw new \InvalidArgumentException('Invalid pesel number: ' . $number);
		}
		$this->number = $number;
	}

	public function getNumber()
	{
		return $this->number;
	}

	public static function isValid($number)
	{
		if (self::PESEL_LENGTH !== strlen($number))
		{
			return false;
		}
		if (false === ctype_digit($number))
		{
			return false;
		}

		$checkSum = self::calculateChecksum($number);
		if ($checkSum !== (int) $number[self::PESEL_LENGTH-1])
		{
			return false;
		}

		return true;
	}

	public static function calculateChecksum($digits)
	{
		$digits = str_split($digits);
		$weights = str_split(self::PESEL_WEIGHTS);
		$sum = 0;

		for ($i = 0; $i < 10; $i++)
		{
			$sum += $digits[$i] * $weights[$i];
		}
		return $sum % 10;
	}

	public static function createFromDateAndNumber(\DateTime $date, $number)
	{
		if (4 !== strlen($number) || false === ctype_digit($number))
		{
			throw new \InvalidArgumentException('Cannot create pesel from this number: ' . $number);
		}

		list ($year, $month, $day) = explode('-', $date->format('y-m-d'));
		switch (floor($year / 100))
		{
			case 18: $month = $month + 80; break;
			case 19: break;
			case 20: $month = $month + 20; break;
			case 21: $month = $month + 40; break;
			case 22: $month = $month + 60; break;
			default: throw new \InvalidArgumentException('Invalid year: ' . $date->format('Y'));
		}
		$digits = sprintf("%02d%02d%02d", $year % 100, $month, $day) . $number;
		return new self($digits . self::calculateChecksum($digits));

	}

	public function getSerialNumber()
	{
		return substr($this->number, 6, 4);
	}

	public function getGender()
	{
		return $this->number[self::PESEL_GENDER_INDEX] % 1 ? self::GENDER_MALE : self::GENDER_FEMALE;
	}

	public function getDate()
	{
		list ($year, $month, $day) = array_map('intval', str_split($this->number, 2));

		switch (ceil($month / 20))
		{
			case 1: $year += 1900; break;
			case 2: $year += 2000; break;
			case 3: $year += 2100; break;
			case 4: $year += 2200; break;
			case 5: $year += 1800; break;
		}
		$month = $month % 20;

		return \DateTime::createFromFormat('Y-m-d', sprintf('%s-%s-%s', $year, $month, $day));
	}


}
