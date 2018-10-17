<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Category;
use App\Entity\Coupon;
use App\Entity\Partner;
use App\Repository\CampaignRepository;
use App\Repository\CategoryRepository;
use App\Repository\CouponRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Xuid\Xuid;

/**
 * @Route("/api/v1")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/users/{username}/coupons", name="api_v1_user_coupon")
     */
    public function userCouponAction(CouponRepository $couponRepo, $username)
    {
        $coupons = $couponRepo->findByUser($username);

        $data = [];
        if ($coupons) {
            foreach ($coupons as $coupon) {
                $data[] = $this->couponToArray($coupon);
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['coupons' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/campaigns/{campaignXuid}/users/{username}/coupons", name="api_v1_campaign_user_coupon")
     */
    public function userCampaignCouponAction(CouponRepository $couponRepo, CampaignRepository $campaignRepo, $campaignXuid, $username)
    {
        if (!$campaign = $campaignRepo->findOneByXuid($campaignXuid)) {
            return new JsonResponse(['code' => 'error', 'message' => 'campaign not found'], Response::HTTP_BAD_REQUEST);
        }

        $coupons = $couponRepo->findByUserAndCampaignId($username, $campaign->getId());
        $data = [];
        if ($coupons) {
            foreach ($coupons as $coupon) {
                $data[] = $this->couponToArray($coupon);
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['coupons' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/campaigns", name="api_v1_campaign_list")
     */
    public function campaignListAction(CampaignRepository $campaignRepo, PartnerRepository $partnerRepository)
    {
        $campaigns = $campaignRepo->findAll();

        $data = [];
        if ($campaigns) {
            foreach ($campaigns as $campaign) {
                $dataArray = [];

                $dataArray = $this->campaignToArray($campaign);
                $dataArray['partner'] = [];
                if ($campaign->getPartnerId()) {
                    $dataArray['partner'] = $this->partnerToArray($partnerRepository->find($campaign->getPartnerId()));
                }
                $data[] = $dataArray;
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['campaigns' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/campaigns/{campaignXuid}", name="api_v1_campaign_view");
     */
    public function campaignViewAction(CampaignRepository $campaignRepo, PartnerRepository $partnerRepository, $campaignXuid)
    {
        $data = [];
        if ($campaign = $campaignRepo->findOneByXuid($campaignXuid)) {
            $data = $this->campaignToArray($campaign);
            if ($campaign->getPartnerId()) {
                $data['partner'] = $this->partnerToArray($partnerRepository->find($campaign->getPartnerId()));
            }
            $data['campaign_categories'] = [];
            foreach ($campaign->getCampaignCategories() as $campaignCategory) {
                $data['campaign_categories'][] = $this->categoryToArray($campaignCategory->getCategory());
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['campaign' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/users/{username}/get-coupon/{campaignXuid}", name="api_v1_user_coupon_create");
     */
    public function createUserCouponAction(CampaignRepository $campaignRepo, $campaignXuid, $username)
    {
        if (!$campaign = $campaignRepo->findOneByXuid($campaignXuid)) {
            return new JsonResponse(['code' => 'error', 'message' => 'campaign not found'], Response::HTTP_BAD_REQUEST);
        }

        $xuid = new Xuid();

        $coupon = new Coupon();
        $coupon->setCampaign($campaign)
            ->setuser($username)
            ->setCreatedAt(time())
            ->setXuid($xuid->getXuid())
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($coupon);
        $em->flush();

        $data = $this->couponToArray($coupon);

        return JsonResponse::fromJsonString(
            json_encode(['coupons' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/partners", name="api_v1_partner_list");
     */
    public function partnerListAction(PartnerRepository $partnerRepository)
    {
        $partners = $partnerRepository->findAll();

        $data = [];
        if ($partners) {
            foreach ($partners as $partner) {
                $data[] = $this->partnerToArray($partner);
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['partners' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/partners/{partnerXuid}", name="api_v1_partner_view");
     */
    public function partnerViewAction(PartnerRepository $partnerRepository, CampaignRepository $campaignRepo, $partnerXuid)
    {
        $data = [];
        if ($partner = $partnerRepository->findOneByXuid($partnerXuid)) {
            $data = $this->partnerToArray($partner);
            $data['campaigns'] = [];

            if ($campaigns = $campaignRepo->findByPartnerId($partner->getId())) {
                if ($campaigns) {
                    foreach ($campaigns as $campaign) {
                        $data['campaigns'][] = $this->campaignToArray($campaign);
                    }
                }
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['partner' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/categories", name="api_v1_category_list");
     */
    public function categoryListAction(CategoryRepository $categoryRepository)
    {
        $data = [];
        if ($categories = $categoryRepository->findAll()) {
            foreach ($categories as $category) {
                $data[] = $this->categoryToArray($category);
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['categories' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/categories/{categoryUri}", name="api_v1_category_view");
     */
    public function categoryViewAction(CategoryRepository $categoryRepository, PartnerRepository $partnerRepository, $categoryUri)
    {
        $data = [];
        if ($category = $categoryRepository->findOneByUri($categoryUri)) {
            $data = $this->categoryToArray($category);
            $data['campaigns'] = [];

            foreach ($category->getCampaignCategories() as $campaignCategory) {
                $arrCampaign = $this->campaignToArray($campaignCategory->getCampaign());
                $data['campaigns'][] = array_merge(
                    $arrCampaign,
                    ['partner' => ($campaignCategory->getCampaign()->getPartnerId()) ? $this->partnerToArray($partnerRepository->find($campaignCategory->getCampaign()->getPartnerId())) : []]
                );
            }
        }

        return JsonResponse::fromJsonString(
            json_encode(['category' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            Response::HTTP_OK
        );
    }

    private function campaignToArray(campaign $campaign)
    {
        $secondaryImageUrls = [];
        if (!empty($campaign->getImageUrls())) {
            $secondaryImageUrls = preg_split('/\r\n|\r|\n/', $campaign->getImageUrls());
        }

        // Set Active status//
        $active = false;
        $currentDatetime = time();
        if ($currentDatetime >= $campaign->getStartAt()) {
            $active = true;
        }
        if ($campaign->getEndAt() && $currentDatetime > $campaign->getEndAt()) {
            $active = false;
        }

        return $data = [
            'name' => $campaign->getName(),
            'xuid' => $campaign->getXuid(),
            'imageUrl' => $campaign->getImageUrl(),
            'description' => $campaign->getDescription(),
            'startAt' => $campaign->getStartAt(),
            'endAt' => $campaign->getEndAt(),
            'details' => $campaign->getDetails(),
            'benefits' => $campaign->getBenefits(),
            'secondaryImageUrls' => $secondaryImageUrls,
            'sharedCode' => $campaign->getSharedCode(),
            'active' => $active,
        ];
    }

    private function couponToArray(coupon $coupon)
    {
        return $data = [
            'campaign' => $coupon->getCampaign()->getName(),
            'xuid' => $coupon->getXuid(),
            'createdAt' => $coupon->getCreatedAt(),
            'user' => $coupon->getUser(),
            'usedAt' => $coupon->getUsedAt(),
        ];
    }

    private function partnerToArray(Partner $partner)
    {
        return $data = [
            'xuid' => $partner->getXuid(),
            'displayName' => $partner->getDisplayName(),
            'status' => $partner->getStatus(),
            'imageUrl' => $partner->getImageUrl(),
            'description' => $partner->getDescription(),
            'website' => $partner->getWebsite(),
            'addressline1' => $partner->getAddressline1(),
            'addressline2' => $partner->getAddressline2(),
            'phone' => $partner->getPhone(),
            'city' => $partner->getCity(),
            'postalcode' => $partner->getPostalcode(),
            'latitude' => $partner->getLatitude(),
            'longitude' => $partner->getLongitude(),
            'email' => $partner->getEmail(),
        ];
    }

    private function categoryToArray(Category $category)
    {
        return $data = [
            'name' => $category->getName(),
            'uri' => $category->getUri(),
            'description' => $category->getDescription(),
        ];
    }
}
