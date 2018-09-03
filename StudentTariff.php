<?php

class StudentTariff extends Tariff
{
	use GPS;
	const MAX_STUDENT_AGE = 25;

	/**
	 * StudentTariff constructor.
	 * @param bool $GPS
	 * @param bool $hiredDriver
	 */
	public function __construct($GPS = false, $hiredDriver = false)
	{
		$this->setMinutePrice(1);
		$this->setKilometerPrice(4);
		$this->setAgeCoefficient(1);

		$this->setGPS($GPS);
		$this->setHiredDriver($hiredDriver);
	}

	/**
	 * @param $kilometers
	 * @param $minutes
	 * @param $age
	 * @return bool|float|int
	 */
	public function calculatePrice($kilometers, $minutes, $age)
	{
		if (!$this->verifyAge($age)) {
			return false;
		}

		$result = ($kilometers * $this->getKilometerPrice() + $minutes * $this->getMinutePrice()) *
			$this->getAgeCoefficient();

		if ($this->getGPS()) {
			$result += $this->calculateGPSPrice($minutes);
		}

		return $result;
	}

	/**
	 * @param $age
	 * @return bool
	 */
	protected function verifyAge($age)
	{
		if ($age < self::MIN_AGE || $age > self::MAX_STUDENT_AGE) {
			return false;
		}

		if ($age >= self::MIN_AGE && $age <= self::LIMIT_COEFFICIENT_AGE) {
			$this->setAgeCoefficient(1.1);
		} else {
			$this->setAgeCoefficient(1);
		}

		return true;
	}
}