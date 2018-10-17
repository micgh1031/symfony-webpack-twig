<?php

namespace App\Controller\Admin;

use App\Entity\Coupon;
use App\Export\CouponExport;
use App\Form\CouponCodeType;
use App\Form\CouponType;
use App\Form\CouponUploadType;
use App\Repository\CampaignRepository;
use App\Repository\CouponRepository;
use DataTable\Core\Reader\Csv as CsvReader;
use DataTable\Core\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Xuid\Xuid;

/**
 * @Route("/admin/campaign/{campaignId}")
 */
class CouponAdminController extends AbstractController
{
    /**
     * @Route("/coupons", name="admin_coupon_index", methods="GET")
     */
    public function indexAction(CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId): Response
    {
        if (!$campaign = $campaignRepo->find($campaignId)) {
            return $this->redirectToRoute('admin_campaign_index');
        }
        // $coupons = $couponRepository->findByCampaignId($campaignId);
        $coupons = $campaign->getCoupons();

        return $this->render('admin/coupon/index.html.twig', ['coupons' => $coupons, 'campaign' => $campaign]);
    }

    /**
     * @Route("/coupons/generate", name="admin_coupon_generate", methods="GET|POST")
     */
    public function generateAction(Request $request, CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId)
    {
        if (!$campaign = $campaignRepo->find($campaignId)) {
            return $this->redirectToRoute('admin_campaign_index');
        }
        $form = $this->createForm(CouponCodeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $xuid = new Xuid();
            for ($i = 0; $i < $data['amount']; ++$i) {
                $uniqueNum = $this->generateUniqueNum($couponRepository, $campaignId);

                $coupon = new Coupon();
                $coupon->setCampaign($campaign)
                    ->setCreatedAt(time())
                    ->setXuid($xuid->getXuid())
                    ->setCode($uniqueNum);

                $em = $this->getDoctrine()->getManager();
                $em->persist($coupon);
                $em->flush();
            }

            return $this->redirectToRoute('admin_coupon_index', [
                'campaignId' => $campaignId,
            ]);
        }

        return $this->render('admin/coupon/generate.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/coupons/upload", name="admin_coupon_upload", methods="GET|POST")
     */
    public function uploadAction(Request $request, CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId)
    {
        if (!$campaign = $campaignRepo->find($campaignId)) {
            return $this->redirectToRoute('admin_campaign_index');
        }
        $form = $this->createForm(CouponUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['csv_file']->getData();
            $table = new Table();
            $table->setName($file->getPathname());

            // Instantiate a Reader, in this case a .csv file reader
            $reader = new CsvReader();
            $reader->setSeperator("\t");
            $reader->loadFile($table, $file->getPathname());

            $xuid = new Xuid();
            foreach ($table->getRows() as $row) {
                $code = $row->getValueByColumnName('code');
                if (empty($code) || $couponRepository->findOneByCampaignIdAndCode($campaignId, $code)) {
                    continue;
                }
                $coupon = new Coupon();
                $coupon->setCampaign($campaign)
                    ->setCreatedAt(time())
                    ->setXuid($xuid->getXuid())
                    ->setCode($code);

                $em = $this->getDoctrine()->getManager();
                $em->persist($coupon);
                $em->flush();
            }

            return $this->redirectToRoute('admin_coupon_index', [
                'campaignId' => $campaignId,
            ]);
        }

        return $this->render('admin/coupon/upload.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/coupons/download", name="admin_coupon_download", methods="GET")
     */
    public function downloadAction(Request $request, CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId)
    {
        if (!$campaign = $campaignRepo->find($campaignId)) {
            return $this->redirectToRoute('admin_campaign_index');
        }
        $export = new CouponExport($campaign->getCoupons());

        return  $export->getResponse();
    }

    /**
     * @Route("/coupons/add", name="admin_coupon_add", methods="GET|POST")
     */
    public function addAction(Request $request, CampaignRepository $campaignRepo, $campaignId): Response
    {
        $coupon = new Coupon();

        return $this->getEditForm($request, $campaignRepo, $coupon, $campaignId);
    }

    /**
     * @Route("/coupons/{id}", name="admin_coupon_view", methods="GET")
     */
    public function viewAction(CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId, $id): Response
    {
        if (!$coupon = $couponRepository->findOneByCampaignIdAndId($campaignId, $id)) {
            return $this->redirectToRoute('admin_coupon_index', [
                'campaignId' => $campaignId,
            ]);
        }

        return $this->render('admin/coupon/view.html.twig', ['coupon' => $coupon]);
    }

    /**
     * @Route("/coupons/{id}/edit", name="admin_coupon_edit", methods="GET|POST")
     */
    public function editAction(Request $request, CouponRepository $couponRepository, CampaignRepository $campaignRepo, $campaignId, $id): Response
    {
        if (!$coupon = $couponRepository->findOneByCampaignIdAndId($campaignId, $id)) {
            return $this->redirectToRoute('admin_coupon_index', [
                'campaignId' => $campaignId,
            ]);
        }

        return $this->getEditForm($request, $campaignRepo, $coupon, $campaignId);
    }

    public function getEditForm(Request $request, CampaignRepository $campaignRepo, Coupon $coupon, $campaignId)
    {
        if (!$campaign = $campaignRepo->find($campaignId)) {
            return $this->redirectToRoute('admin_campaign_index');
        }

        if (!$coupon->getId()) {
            $coupon->setCampaign($campaign);
        }

        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$coupon->getId()) {
                $xuid = new Xuid();
                $coupon->setCreatedAt(time())
                 ->setXuid($xuid->getXuid());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('admin_coupon_index', [
                'campaignId' => $campaignId,
            ]);
        }

        return $this->render('admin/coupon/edit.html.twig', [
            'coupon' => $coupon,
            'campaignId' => $campaignId,
            'campaign' => $campaign,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/coupons/{id}/delete", name="admin_coupon_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Coupon $coupon, $campaignId): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($coupon);
        $em->flush();

        return $this->redirectToRoute('admin_coupon_index', ['campaignId' => $campaignId]);
    }

    private function generateUniqueNum(CouponRepository $couponRepository, $campaignId)
    {
        randomLabel:
        $random = mt_rand(10000000, 99999999);

        if ($couponRepository->findOneByCampaignIdAndCode($campaignId, $random)) {
            goto randomLabel;
        }

        return $random;
    }
}
