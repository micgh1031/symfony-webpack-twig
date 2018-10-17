<?php

namespace App\Controller;

use App\Repository\CouponRepository;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{
    /**
     * @Route("/coupons/{couponXuid}", name="coupon_show")
     */
    public function showAction(CouponRepository $couponRepo, $couponXuid)
    {
        if (!$coupon = $couponRepo->findOneByXuid($couponXuid)) {
            return $this->redirectToRoute('frontpage');
        }

        return $this->render('coupon/show.html.twig', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * @Route("/coupons/{couponXuid}/qr", name="coupon_qrcode")
     */
    public function qrCodeAction(CouponRepository $couponRepo, $couponXuid)
    {
        if (!$coupon = $couponRepo->findOneByXuid($couponXuid)) {
            return $this->redirectToRoute('frontpage');
        }

        // https://github.com/endroid/qr-code#symfony-integration
        // Create a basic QR code
        $qrCode = new QrCode(getenv('QR_CODE_URL').'/coupons/'.$couponXuid);
        $qrCode->setSize(300);

        // Create a response object
        return $response = new QrCodeResponse($qrCode);
    }
}
