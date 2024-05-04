<?php

namespace App\Service;

use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class DataSaver {
	private EntityManagerInterface $em;
	/** @var array<string, OpeningHours> */
	private array $openingHoursCache;

	public function __construct(EntityManagerInterface $em) {
		$this->em = $em;
		$this->openingHoursCache = array();
	}

	/**
	 * @description Save data to database
	 * @param array<PointOfSale> $data
	 *
	 * @return void
	 */
	public function saveData(array $data): void {
		foreach ($data as $point) {
			if ($point instanceof PointOfSale) {
				// Check if point already exists
				$existingPoint = $this->em->getRepository(PointOfSale::class)->findOneBy([
					'id' => $point->getId(),
				]);
				if ($existingPoint) {
					// update changes to existing and set $point to existing one
					$existingPoint->setType($point->getType());
					$existingPoint->setName($point->getName());
					$existingPoint->setAddress($point->getAddress());
					$existingPoint->setLatitude($point->getLatitude());
					$existingPoint->setLongitude($point->getLongitude());
					$existingPoint->setServices($point->getServices());
					$existingPoint->setPayMethods($point->getPayMethods());

					$point = $existingPoint;
				}
				// Save opening hours
				foreach ($point->getOpeningHours() as $openingHour) {
					if (!$openingHour instanceof OpeningHours) {
						continue;
					}
					$key = $openingHour->getKey();
					// Check if opening hour already exists
					if (!isset($this->openingHoursCache[$key])){
						$found = $this->em->getRepository(OpeningHours::class)->findOneBy([
							'dayFrom' => $openingHour->getDayFrom(),
							'dayTo' => $openingHour->getDayTo(),
							'timeFrom' => $openingHour->getTimeFrom(),
							'timeTo' => $openingHour->getTimeTo(),
						]);
						// If found, set it to cache
						if ($found) {
							$this->openingHoursCache[$key] = $found;
						}
					}
					// If opening hour already exists, remove the new one and add the existing one
					$existingOpeningHour = $this->openingHoursCache[$key] ?? null;
					// If opening hour is not in cache, add it to cache
					if ($existingOpeningHour) {
						$point->removeOpeningHour($openingHour);
						$point->addOpeningHour($existingOpeningHour);
					} else {
						// If opening hour is not in cache, add it to cache and persist it
						$this->em->persist($openingHour);
						$this->openingHoursCache[$key] = $openingHour;
					}
				}

				// Persist point
				$this->em->persist($point);
			}
		}
		// Flush changes
		$this->em->flush();
	}
}