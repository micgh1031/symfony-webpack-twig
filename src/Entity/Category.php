<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uri;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CampaignCategory", mappedBy="category")
     */
    private $campaignCategories;

    public function __construct()
    {
        $this->campaignCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|CampaignCategory[]
     */
    public function getCampaignCategories(): Collection
    {
        return $this->campaignCategories;
    }

    public function addCampaignCategory(CampaignCategory $campaignCategory): self
    {
        if (!$this->campaignCategories->contains($campaignCategory)) {
            $this->campaignCategories[] = $campaignCategory;
            $campaignCategory->setCategory($this);
        }

        return $this;
    }

    public function removeCampaignCategory(CampaignCategory $campaignCategory): self
    {
        if ($this->campaignCategories->contains($campaignCategory)) {
            $this->campaignCategories->removeElement($campaignCategory);
            // set the owning side to null (unless already changed)
            if ($campaignCategory->getCategory() === $this) {
                $campaignCategory->setCategory(null);
            }
        }

        return $this;
    }
}
