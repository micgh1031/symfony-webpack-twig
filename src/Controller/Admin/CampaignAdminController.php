<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use App\Entity\CampaignCategory;
use App\Form\CampaignCategoryType;
use App\Form\CampaignType;
use App\Repository\CampaignCategoryRepository;
use App\Repository\CampaignRepository;
use App\Repository\CategoryRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Xuid\Xuid;

/**
 * @Route("/admin")
 */
class CampaignAdminController extends AbstractController
{
    /**
     * @Route("/campaigns", name="admin_campaign_index", methods="GET")
     */
    public function indexAction(CampaignRepository $campaignRepository): Response
    {
        return $this->render('admin/campaign/index.html.twig', ['campaigns' => $campaignRepository->findAll()]);
    }

    /**
     * @Route("/campaigns/add", name="admin_campaign_add", methods="GET|POST")
     */
    public function addAction(Request $request, PartnerRepository $partnerRepository): Response
    {
        $campaign = new Campaign();

        return $this->getEditForm($request, $partnerRepository, $campaign);
    }

    /**
     * @Route("/campaigns/{id}", name="admin_campaign_view", methods="GET")
     */
    public function viewAction(Campaign $campaign, PartnerRepository $partnerRepository): Response
    {
        $partner = $partnerRepository->find((int) $campaign->getPartnerId());

        return $this->render('admin/campaign/view.html.twig', ['campaign' => $campaign, 'partner' => $partner]);
    }

    /**
     * @Route("/campaigns/{id}/edit", name="admin_campaign_edit", methods="GET|POST")
     */
    public function edit(Request $request, PartnerRepository $partnerRepository, Campaign $campaign): Response
    {
        return $this->getEditForm($request, $partnerRepository, $campaign);
    }

    protected function getEditForm(Request $request, PartnerRepository $partnerRepository, Campaign $campaign)
    {
        $partnerArray = [];
        $partners = $partnerRepository->findAll();
        foreach ($partners as $partner) {
            $partnerArray[$partner->getId()] = $partner->getDisplayName();
        }

        $form = $this->createForm(CampaignType::class, $campaign, ['partnerArray' => $partnerArray]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$campaign->getId()) {
                $xuid = new Xuid();
                $campaign->setXuid($xuid->getXuid());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($campaign);
            $em->flush();

            return $this->redirectToRoute('admin_campaign_index');
        }

        return $this->render('admin/campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/campaigns/{id}/categories/assign", name="admin_campaign_category_assign", methods="GET|POST")
     */
    public function categoryAssignAction(Request $request, CategoryRepository $categoryRepository, CampaignCategoryRepository $campaignCategoryRepository, Campaign $campaign)
    {
        $campaignCategoryArray = [];

        foreach ($campaign->getCampaignCategories() as $campaignCategory) {
            // code...
            $campaignCategoryArray[] = $campaignCategory->getCategory()->getId();
        }

        $form = $this->createForm(CampaignCategoryType::class, null, ['campaignCategoryArray' => $campaignCategoryArray]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $selectArray = array_diff($data['categories'], $campaignCategoryArray);

            foreach ($selectArray as $categoryId) {
                $campaignCategory = new CampaignCategory();
                $campaignCategory
                    ->setCampaign($campaign)
                    ->setCategory($categoryRepository->find($categoryId));

                $em->persist($campaignCategory);
                $em->flush();
            }

            // Delete  Unselect //
            $unSelecteArray = array_diff($campaignCategoryArray, $data['categories']);
            foreach ($unSelecteArray as $categoryId) {
                if ($campaignCategory = $campaignCategoryRepository->findOneByCampaignIdAndCategoryId($campaign->getId(), $categoryId)) {
                    $em->remove($campaignCategory);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin_campaign_view', [
                'id' => $campaign->getId(),
            ]);
        }

        return $this->render('admin/campaign/category.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }
}
