<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PointOfSale {
	#[ORM\Id]
	#[ORM\Column(type: "string", nullable: false)]
	private string $id;
	#[ORM\Column(type: "string", nullable: false)]
	private string $type;
	#[ORM\Column(type: "string", nullable: false)]
	private string $name;
	#[ORM\Column(type: "string", nullable: true)]
	private string|null $address;
	#[ORM\ManyToMany(targetEntity: OpeningHours::class, inversedBy: "pointOfSales")]
	private Collection $openingHours;
	#[ORM\Column(type: "float", nullable: false)]
	private float $latitude;
	#[ORM\Column(type: "float", nullable: false)]
	private float $longitude;
	#[ORM\Column(type: "integer", nullable: false)]
	private int $services;
	#[ORM\Column(type: "integer", nullable: false)]
	private int $payMethods;
	#[ORM\Column(type: "string", nullable: true)]
	private string $link;

	public function __construct() {
		$this->openingHours = new ArrayCollection();
	}

	public function setId(string $id): PointOfSale {
		$this->id = $id;
		return $this;
	}

	public function getId(): string {
		return $this->id;
	}

	public function setType(string $type): PointOfSale {
		$this->type = $type;
		return $this;
	}

	public function getType(): string {
		return $this->type;
	}

	public function setName(string $name): PointOfSale {
		$this->name = $name;
		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setAddress(?string $address): PointOfSale {
		$this->address = $address;
		return $this;
	}

	public function getAddress(): ?string {
		return $this->address;
	}

	public function setLatitude(float $latitude): PointOfSale {
		$this->latitude = $latitude;
		return $this;
	}

	public function getLatitude(): float {
		return $this->latitude;
	}

	public function setLongitude(float $longitude): PointOfSale {
		$this->longitude = $longitude;
		return $this;
	}

	public function getLongitude(): float {
		return $this->longitude;
	}

	public function setServices(int $services): PointOfSale {
		$this->services = $services;
		return $this;
	}

	public function getServices(): int {
		return $this->services;
	}

	public function setPayMethods(int $payMethods): PointOfSale {
		$this->payMethods = $payMethods;
		return $this;
	}

	public function getPayMethods(): int {
		return $this->payMethods;
	}

	public function setLink(string $link): PointOfSale {
		$this->link = $link;
		return $this;
	}

	public function getLink(): string {
		return $this->link;
	}

	public function addOpeningHour(OpeningHours $oh): self {
		if (!$this->openingHours->contains($oh)) {
			$this->openingHours->add($oh);
			$oh->addPointOfSale($this);
		}
		return $this;
	}

	public function removeOpeningHour(OpeningHours $oh) : self {
		if ($this->openingHours->contains($oh)) {
			$this->openingHours->removeElement($oh);
			$oh->removePointOfSale($this);
		}
		return $this;
	}


	public function getOpeningHours(): Collection {
		return $this->openingHours;
	}

	public function __toString(): string {
		return $this->name." (".$this->id.")" . " - " . $this->address . " - " . $this->latitude . " - " . $this->longitude . " - " . $this->services . " - " . $this->payMethods;
	}
}