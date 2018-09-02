<?php

interface TariffInterface
{
	public function calculatePrice($kilometers, $minutes, $age);
}

abstract class Tariff implements TariffInterface
{
	abstract protected function verifyAge($age);

	protected $minutePrice;
	protected $kilometerPrice;
	protected $ageCoefficient;

	protected $GPS;
	protected $HiredDriver;

	const MIN_AGE = 18;
	const LIMIT_COEFFICIENT_AGE = 21;
	const MAX_AGE = 65;

	protected function getHiredDriver()
	{
		return $this->HiredDriver;
	}

	protected function setHiredDriver($HiredDriver): void
	{
		$this->HiredDriver = $HiredDriver;
	}

	protected function getGPS()
	{
		return $this->GPS;
	}

	protected function setGPS($GPS): void
	{
		$this->GPS = $GPS;
	}

	protected function getKilometerPrice()
	{
		return $this->kilometerPrice;
	}

	protected function setKilometerPrice($kilometerPrice)
	{
		$this->kilometerPrice = $kilometerPrice;
	}

	protected function getAgeCoefficient()
	{
		return $this->ageCoefficient;
	}

	protected function setAgeCoefficient($ageCoefficient)
	{
		$this->ageCoefficient = $ageCoefficient;
	}

	protected function getMinutePrice()
	{
		return $this->minutePrice;
	}

	protected function setMinutePrice($minutePrice)
	{
		$this->minutePrice = $minutePrice;
	}


}

trait GPS
{
	private $hourlyPrice = 15;
	private $minuteInHour = 60;

	public function getHourlyPrice()
	{
		return $this->hourlyPrice;
	}

	public function getMinuteInHour()
	{
		return $this->minuteInHour;
	}

	/**
	 * @param $minutes
	 * @return float|int
	 */
	protected function calculateGPSPrice($minutes)
	{
		$hours = $minutes / 60;

		if ($hours - floor($hours) != 0) {
			$hours = floor($hours) + 1;
		}
		return $hours * $this->getHourlyPrice();
	}
}

trait  HiredDriver
{
	protected $price = 100;

	/**
 * @return int
 */
	protected function getHiredDriverPrice()
	{
		return $this->price;
	}
}

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

