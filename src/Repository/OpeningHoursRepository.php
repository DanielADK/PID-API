<?php

namespace App\Repository;

use App\Entity\OpeningHours;
use App\Entity\PointOfSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OpeningHoursRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, OpeningHours::class);
	}

	/**
	 * @description Find points of sale that are open at given time
	 *
	 * @param string $time
	 * @param string $date
	 *
	 * @return array<PointOfSale>
	 */
	public function findOpenAt(string $time, string $date): array {
		// Get day of the week
		$dayOfWeek = date("N", strtotime($date))-1;

		// Create query builder
		$qb = $this->createQueryBuilder("oh");

		// Join with pointOfSales
		$qb->join("oh.pointOfSales", "pos");

		// Add conditions to the query
		$qb->where(":dayOfWeek BETWEEN oh.dayFrom AND oh.dayTo")
		   ->andWhere(":time BETWEEN oh.timeFrom AND oh.timeTo")
		   ->setParameter("dayOfWeek", $dayOfWeek)
		   ->setParameter("time", $time);
		$openingHours = $qb->getQuery()->getResult();

		// Extract PointOfSale from OpeningHours
		$pointsOfSale = array();
		foreach($openingHours as $oh) {
			foreach ($oh->getPointOfSales() as $pointOfSale) {
				$pointsOfSale[] = $pointOfSale;
			}
		}

		return $pointsOfSale;
	}
}