<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OpeningHoursRepository;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'openinghoursIdx', columns: ["day_from", "day_to", "time_from", "time_to"])]
class OpeningHours {
	#[ORM\Id]
	#[ORM\Column(type: "integer", nullable: false)]
	#[ORM\GeneratedValue]
	private int $id;
	#[ORM\Column(name: "day_from", type: "integer", nullable: false)]
	private int $dayFrom;
	#[ORM\Column(name: "day_to", type: "integer", nullable: false)]
	private int $dayTo;
	#[ORM\Column(name: "time_from", type: "time", nullable: false)]
	private \DateTimeInterface $timeFrom;
	#[ORM\Column(name: "time_to", type: "time", nullable: false)]
	private \DateTimeInterface $timeTo;
	/** @var Collection<string, PointOfSale> */
	#[ORM\ManyToMany(targetEntity: PointOfSale::class, mappedBy: "openingHours", cascade: ["persist"])]
	private Collection $pointOfSales;

	public function __construct() {
		$this->pointOfSales = new ArrayCollection();
	}

	public function getId(): int {
		return $this->id;
	}

	public function setDayFrom(int $dayFrom): OpeningHours {
		$this->dayFrom = $dayFrom;
		return $this;
	}

	public function getDayFrom(): int {
		return $this->dayFrom;
	}

	public function setDayTo(int $dayTo): OpeningHours {
		$this->dayTo = $dayTo;
		return $this;
	}

	public function getDayTo(): int {
		return $this->dayTo;
	}

	public function setTimeFrom(\DateTimeInterface $timeFrom): OpeningHours {
		$this->timeFrom = $timeFrom;
		return $this;
	}

	public function getTimeFrom(): \DateTimeInterface {
		return $this->timeFrom;
	}

	public function setTimeTo(\DateTimeInterface $timeTo): OpeningHours {
		$this->timeTo = $timeTo;
		return $this;
	}

	public function getTimeTo(): \DateTimeInterface {
		return $this->timeTo;
	}

	/**
	 * @description Returns collection of point of sales
	 * @return Collection<string, PointOfSale>
	 */
	public function getPointOfSales(): Collection {
		return $this->pointOfSales;
	}

	/**
	 * @description Returns a unique key for the object
	 * @return string
	 */
	public function getKey(): string {
		return $this->dayFrom . $this->dayTo . $this->timeFrom->format("H:i") . $this->timeTo->format("H:i");
	}

	/**
	 * @description Add point of sale to opening hours
	 * @param PointOfSale $pos
	 * @return $this
	 */
	public function addPointOfSale(PointOfSale $pos): self {
		// Id key for O(1) lookup
		$key = $pos->getId();
		if (!isset($this->pointOfSales[$key])) {
			$this->pointOfSales[$key] = $pos;
			$pos->addOpeningHour($this);
		}
		return $this;
	}

	/**
	 * @description Remove point of sale from opening hours
	 * @param PointOfSale $pos
	 * @return $this
	 */
	public function removePointOfSale(PointOfSale $pos): self {
		// Id key for O(1) lookup
		$key = $pos->getId();
		if (isset($this->pointOfSales[$key])) {
			unset($this->pointOfSales[$key]);
			$pos->removeOpeningHour($this);
		}
		return $this;
	}

	public function __toString(): string {
		return $this->dayFrom . " - " . $this->dayTo . " - " . $this->timeFrom->format("H:i") . " - " . $this->timeTo->format("H:i");
	}
}