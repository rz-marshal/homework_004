<?php

class BaseTariff extends Tariff
{
	use GPS;

	/**
	 * BaseTariff constructor.
	 * @param bool $GPS
	 */
	public function __construct($GPS = false)
	{
		$this->setMinutePrice(3);
		$this->setKilometerPrice(10);
		$this->setAgeCoefficient(1);
		$this->setGPS($GPS);
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

		$result = $kilometers * $this->getKilometerPrice() + $minutes * $this->getMinutePrice() *
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