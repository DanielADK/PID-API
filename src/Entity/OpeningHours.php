<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'openinghoursIdx', columns: ["open_from", "open_to", "hours"])]
class OpeningHours {
	#[ORM\Id]
	#[ORM\Column(type: "integer")]
	#[ORM\GeneratedValue]
	private int $id;
	#[ORM\Column(type: "integer")]
	private int $open_from;
	#[ORM\Column(type: "integer")]
	private int $open_to;
	#[ORM\Column(type: "string")]
	private string $hours;
	/** @var Collection<string, PointOfSale> */
	#[ORM\ManyToMany(targetEntity: PointOfSale::class, mappedBy: "openingHours", cascade: ["persist"])]
	private Collection $pointOfSales;

	public function __construct() {
		$this->pointOfSales = new ArrayCollection();
	}

	public function getId(): string {
		return $this->id;
	}

	public function setOpenFrom(int $open_from): OpeningHours {
		$this->open_from = $open_from;
		return $this;
	}

	public function getOpenFrom(): int {
		return $this->open_from;
	}

	public function setOpenTo(int $open_to): OpeningHours {
		$this->open_to = $open_to;
		return $this;
	}

	public function getOpenTo(): int {
		return $this->open_to;
	}

	public function setHours(string $hours): OpeningHours {
		$this->hours = $hours;
		return $this;
	}

	public function getHours(): string {
		return $this->hours;
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
		return $this->open_from . $this->open_to . $this->hours;
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
		return $this->id ?? "null" . $this->open_from . " - " . $this->open_to . " - " . $this->hours;
	}
}