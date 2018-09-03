<?php

class DailyTariff extends Tariff
{
	use GPS;
	use HiredDriver;

	private $dailyPrice;

	public function getDailyPrice()
	{
		return $this->dailyPrice;
	}

	public function setDailyPrice($dailyPrice)
	{
		$this->dailyPrice = $dailyPrice;
	}

	/**
	 * DailyTariff constructor.
	 * @param bool $GPS
	 * @param bool $hiredDriver
	 */
	public function __construct($GPS = false, $hiredDriver = false)
	{
		$this->setKilometerPrice(1);
		$this->setAgeCoefficient(1);
		$this->setDailyPrice(1000);

		$this->setGPS($GPS);
		$this->setHiredDriver($hiredDriver);
	}

	/**
	 * @param int $kilometers
	 * @param int $minutes
	 * @param int $age
	 * @return float|int
	 */
	public function calculatePrice($kilometers, $minutes, $age)
	{
		$hours = $minutes / 60;

		if($hours < 24) {
			$hours = 24;
		}

		$days = floor($hours / 24);

		if ($hours - floor($hours) > 0.5) {
			$days = floor(floor($hours) / 24) + 1;
		}

		$result = (($days * $this->getDailyPrice()) + ($kilometers * $this->getKilometerPrice())) *
			$this->getAgeCoefficient();

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