<?php

namespace App\Controller;

use App\Repository\CampaignRepository;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CampaignController extends AbstractController
{
    /**
     * @Route("/campaigns/{campaignXuid}", name="campaign_show")
     */
    public function showAction(CampaignRepository $campaignRepo, $campaignXuid)
    {
        if (!$campaign = $campaignRepo->findOneByXuid($campaignXuid)) {
            return $this->redirectToRoute('frontpage');
        }

        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * @Route(" /campaigns/{campaignXuid}/qr", name="campaign_qrcode")
     */
    public function qrCodeAction(CampaignRepository $campaignRepo, $campaignXuid)
    {
        if (!$campaign = $campaignRepo->findOneByXuid($campaignXuid)) {
            return $this->redirectToRoute('frontpage');
        }

        // Create a basic QR code
        $qrCode = new QrCode(getenv('QR_CODE_URL').'/campaigns/'.$campaignXuid);
        $qrCode->setSize(300);

        // Create a response object
        return $response = new QrCodeResponse($qrCode);
    }
}
