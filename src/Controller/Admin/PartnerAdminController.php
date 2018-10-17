<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Xuid\Xuid;

/**
 * @Route("/admin")
 */
class PartnerAdminController extends AbstractController
{
    /**
     * @Route("/partners", name="admin_partner_index", methods="GET")
     */
    public function indexAction(PartnerRepository $partnerRepository): Response
    {
        return $this->render('admin/partner/index.html.twig', ['partners' => $partnerRepository->findAll()]);
    }

    /**
     * @Route("/partners/add", name="admin_partner_add", methods="GET|POST")
     */
    public function addAction(Request $request): Response
    {
        $partner = new Partner();

        return $this->getEditForm($request, $partner);
    }

    /**
     * @Route("/partners/{id}/edit", name="admin_partner_edit", methods="GET|POST")
     */
    public function edit(Request $request, Partner $partner): Response
    {
        return $this->getEditForm($request, $partner);
    }

    protected function getEditForm(Request $request, Partner $partner)
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$partner->getId()) {
                $xuid = new Xuid();
                $partner->setXuid($xuid->getXuid());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($partner);
            $em->flush();

            return $this->redirectToRoute('admin_partner_index');
        }

        return $this->render('admin/partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }
}
