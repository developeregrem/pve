<?php

/*
 * This file is part of the guesthouse administration package.
 *
 * (c) Alexander Elchlepp <alex.pensionsverwaltung@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\CSRFProtectionService;
use App\Service\AppartmentService;
use App\Entity\Appartment;
use App\Entity\Subsidiary;

class AppartmentServiceController extends AbstractController
{
    public function __construct()
    {
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $appartments = $em->getRepository(Appartment::class)->findAll();

        return $this->render('Appartments/index.html.twig', array(
            "appartments" => $appartments
        ));
    }

    public function getAppartmentAction(CSRFProtectionService $csrf, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $appartment = $em->getRepository(Appartment::class)->find($id);
        $objects = $em->getRepository(Subsidiary::class)->findAll();

        return $this->render('Appartments/appartment_form_edit.html.twig', array(
            'objects' => $objects,
            'appartment' => $appartment,
            'token' => $csrf->getCSRFTokenForForm()
        ));
    }

    public function newAppartmentAction(CSRFProtectionService $csrf)
    {
        $em = $this->getDoctrine()->getManager();

        $objects = $em->getRepository(Subsidiary::class)->findAll();

        return $this->render('Appartments/appartment_form_create.html.twig', array(
            'objects' => $objects,
            "appartment" => new Appartment(),
            'token' => $csrf->getCSRFTokenForForm()
        ));
    }

    public function createAppartmentAction(CSRFProtectionService $csrf, AppartmentService $as, Request $request)
    {
        $error = false;
        if (($csrf->validateCSRFToken($request))) {
            /* @var $appartment Appartment */
            $appartment = $as->getAppartmentFromForm($request, "new");

            // check for mandatory fields
            if (strlen($appartment->getNumber()) == 0 || strlen($appartment->getBedsMin()) == 0 || strlen($appartment->getBedsMax()) == 0
                || strlen($appartment->getDescription()) == 0
            ) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($appartment);
                $em->flush();

                // add succes message
                $this->addFlash('success', 'appartment.flash.create.success');
            }
        }

        return $this->render('Appartments/appartment_feedback.html.twig', array(
            "error" => $error
        ));
    }


    public function editAppartmentAction(CSRFProtectionService $csrf, AppartmentService $as, Request $request, $id)
    {
        $error = false;
        if (($csrf->validateCSRFToken($request))) {
            /* @var $appartment Appartment */
            $appartment = $as->getAppartmentFromForm($request, $id);

            // check for mandatory fields
            if (strlen($appartment->getNumber()) == 0 || strlen($appartment->getBedsMin()) == 0 || strlen($appartment->getBedsMax()) == 0
                || strlen($appartment->getDescription()) == 0
            ) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($appartment);
                $em->flush();

                // add success message           
                $this->addFlash('success', 'appartment.flash.edit.success');
            }
        }

        return $this->render('Appartments/appartment_feedback.html.twig', array(
            "error" => $error
        ));
    }

    public function deleteAppartmentAction(CSRFProtectionService $csrf, AppartmentService $as, Request $request, $id)
    {
        if ($request->getMethod() == 'POST') {
            if (($csrf->validateCSRFToken($request, true))) {
                $appartment = $as->deleteAppartment($id);

                if ($appartment) {
                    $this->addFlash('success', 'appartment.flash.delete.success');
                } else {
                    $this->addFlash('warning', 'appartment.flash.delete.error.still.in.use');
                }
            }
            return new Response("ok");
        } else {
            // initial get load (ask for deleting)           
            return $this->render('Appartments/appartment_form_delete.html.twig', array(
                "id" => $id,
                'token' => $csrf->getCSRFTokenForForm()
            ));
        }
    }
}