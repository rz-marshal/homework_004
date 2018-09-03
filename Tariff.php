<?php

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


