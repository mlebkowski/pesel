<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-31-01 13:03
 */

namespace Nassau\PESEL;

class PESELTest extends TestCase
{

	/**
     * @expectedException \InvalidArgumentException
	 */
	public function testConstructorThrowsOnInvalidInput()
	{
		new PESEL("this is not a valid pesel");
	}

	/**
     * @dataProvider dpGeneratePesels
	 */
	public function testSyntaxValidation($pesel, $isValid)
	{
		$this->assertEquals($isValid, PESEL::isValid($pesel));
	}

	/**
	 * @dataProvider dpPeselAndDates
	 */
	public function testCreateFromDateAndNumber($pesel, $date, $number)
	{
		$this->assertEquals($pesel, PESEL::createFromDateAndNumber(new \DateTime($date), $number)->getNumber());
	}

	/**
	 * @dataProvider dpPeselAndDates
	 */
	public function testGetDate($pesel, $date)
	{
		$pesel = new PESEL($pesel);
		$this->assertEquals($date, $pesel->getDate()->format('Y-m-d'));
	}

	/**
     * @dataProvider dpGetGender
	 */
	public function testGetGender($pesel, $gender)
	{
		$pesel = new PESEL($pesel);
		$this->assertEquals($gender, $pesel->getGender());
	}

	/**
	 * @dataProvider dpGetGender
	 */
	public function testCalculateChecksum($pesel)
	{
		$expected = substr($pesel, PESEL::PESEL_LENGTH-1);
		$this->assertEquals($expected, PESEL::calculateChecksum($pesel));
	}

	public function dpGeneratePesels()
	{
		return [
			'too short'        => ['3812262350'  , false],
			'non-digit'        => ['3812262350s' , false],
			'too long'         => ['381226235022', false],
			'not trimed'       => ['38122623502 ', false],
			'valid one'        => ['38122623502' , true ],
			'all zero'         => ['00000000000', false],

			'invalid checksum' => ['38122623503' , false],
			'invalid date'     => ['38124623500' , true], // date is not checked!
		];
	}

	public function dpPeselAndDates()
	{
		return [
			'before 1900' => ['56840865851', '1856-04-08', '6585'],
			'in XX century' => ['96040932711', '1996-04-09', '3271'],
			'in XXI century' => ['05281185394', '2005-08-11', '8539'],
		];

	}

	public function dpGetGender()
	{
		return [
			[78121319174, PESEL::GENDER_MALE],
			[49072652133, PESEL::GENDER_MALE],
			[42102515479, PESEL::GENDER_MALE],
			[69030903853, PESEL::GENDER_MALE],

			[40052076204, PESEL::GENDER_FEMALE],
			[32041454766, PESEL::GENDER_FEMALE],
			[47040971585, PESEL::GENDER_FEMALE],
			[77122351880, PESEL::GENDER_FEMALE],
		];
	}


}
