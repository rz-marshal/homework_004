<?php

class HourlyTariff extends Tariff
{
	use GPS;
	use HiredDriver;

	/**
	 * HourlyTariff constructor.
	 * @param bool $GPS
	 * @param bool $hiredDriver
	 */
	public function __construct($GPS = false, $hiredDriver = false)
	{
		$this->setMinutePrice(200);
		$this->setAgeCoefficient(1);
		$this->setGPS($GPS);
		$this->setHiredDriver($hiredDriver);
	}

	/**
	 * @param int $minutes
	 * @param int $age
	 * @param int $kilometers
	 * @return bool|float|int
	 */
	public function calculatePrice($minutes, $age, $kilometers = 0)
	{
		if (!$this->verifyAge($age)) {
			return false;
		}

		$hours = $minutes / $this->getMinuteInHour();

		if ($hours - floor($hours) != 0) {
			$hours = floor($hours) + 1;
		}

		$result = $hours * $this->getMinutePrice() * $this->getAgeCoefficient();

		if ($this->getGPS()) {
			$result += $this->calculateGPSPrice($minutes);
		}

		if ($this->getHiredDriver()) {
			$result += $this->getHiredDriverPrice();
		}

		return $result;
	}

	/**
	 * @param $age
	 * @return bool
	 */
	protected function verifyAge($age)
	{
		if ($age < self::MIN_AGE || $age > self::MAX_AGE) {
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