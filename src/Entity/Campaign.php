<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CampaignRepository")
 */
class Campaign
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $xuid;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $start_at;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $end_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Coupon", mappedBy="campaign", orphanRemoval=true)
     */
    private $coupons;

    /**
     * @ORM\Column(type="integer")
     */
    private $partner_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_urls;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $benefits;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $shared_code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CampaignCategory", mappedBy="campaign")
     */
    private $campaignCategories;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
        $this->campaignCategories = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getXuid(): ?string
    {
        return $this->xuid;
    }

    public function setXuid(string $xuid): self
    {
        $this->xuid = $xuid;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getStartAt(): ?int
    {
        return $this->start_at;
    }

    public function setStartAt(?int $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?int
    {
        return $this->end_at;
    }

    public function setEndAt(?int $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    /**
     * @return Collection|Coupon[]
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupon(Coupon $coupon): self
    {
        if (!$this->coupons->contains($coupon)) {
            $this->coupons[] = $coupon;
            $coupon->setCampaignId($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        if ($this->coupons->contains($coupon)) {
            $this->coupons->removeElement($coupon);
            // set the owning side to null (unless already changed)
            if ($coupon->getCampaignId() === $this) {
                $coupon->setCampaignId(null);
            }
        }

        return $this;
    }

    public function getPartnerId(): ?int
    {
        return $this->partner_id;
    }

    public function setPartnerId(int $partner_id): self
    {
        $this->partner_id = $partner_id;

        return $this;
    }

    public function getImageUrls(): ?string
    {
        return $this->image_urls;
    }

    public function setImageUrls(?string $image_urls): self
    {
        $this->image_urls = $image_urls;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getBenefits(): ?string
    {
        return $this->benefits;
    }

    public function setBenefits(?string $benefits): self
    {
        $this->benefits = $benefits;
        return $this;
    }

    public function getSharedCode(): ?string
    {
        return $this->shared_code;
    }

    public function setSharedCode(?string $shared_code): self
    {
        $this->shared_code = $shared_code;
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
            $campaignCategory->setCampaign($this);
        }

        return $this;
    }

    public function removeCampaignCategory(CampaignCategory $campaignCategory): self
    {
        if ($this->campaignCategories->contains($campaignCategory)) {
            $this->campaignCategories->removeElement($campaignCategory);
            // set the owning side to null (unless already changed)
            if ($campaignCategory->getCampaign() === $this) {
                $campaignCategory->setCampaign(null);
            }
        }

        return $this;
    }
}
