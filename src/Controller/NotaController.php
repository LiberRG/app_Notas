<?php

namespace App\Controller;

use App\Entity\Nota;
use App\Service\NotaService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NotaController extends AbstractController
{
    //Opción A
    // #[Route('/nota/new', name: 'app_nota_new')]
    // public function index(EntityManagerInterface $em): Response
    // {
    //     $nota = new Nota();
    //     $nota->setTitulo("Mi primera nota");
    //     $nota->setDescripcion("Mi primera nota con Symfony y Doctrine");

    //     $em->persist($nota);
    //     $em->flush();


    //     return $this->render('nota/index.html.twig', [
    //         'controller_name' => 'NotaController',
    //         'nota' => $nota
    //     ]);
    // }


    //Opción B)
    #[Route('/nota/new', name: 'app_nota_new')]
    public function crear(Request $request, NotaService $notaService): Response
    {
        $nota = new Nota();
        $valid = false;

        if ($request->getMethod() === 'POST') {
            $valid = true;
            //Se usa $request->request para obtener variables enviadas por POST
            //Se usa $request->query para obtener variables enviadas por GET
            $titulo = $request->request->get('titulo');
            $desc =  $request->request->get('desc');

            if (isset($titulo) && empty($titulo)) {
                $this->addFlash('warning', "El campo título es obligatorio");
                $valid = false;
            } else {
                $nota->setTitulo($titulo);
            }

            if (isset($desc) && empty($desc)) {
                $this->addFlash('warning', "El campo descripción es obligatorio");
                $valid = false;
            } else {
                $nota->setDescripcion($desc);
            }
        }

        if ($valid) {
            $nota = $notaService->create($nota);
            $this->addFlash('success', "Se ha creado la nota correctamente");
            return $this->redirectToRoute('app_nota_list');
        } else {

            return $this->render(
                'nota/crear.html.twig',
                ['nota' => $nota]
            );
        }
    }


    #[Route('/nota', name: 'app_nota_list')]
    public function list(NotaService $notaService): Response
    {


        $notas = $notaService->list();
        $contador = sizeof($notas);
        if ($contador > 0) {

            $this->addFlash("info", "Se han encontrado $contador notas");
        }

        return $this->render('nota/list.html.twig', [
            'controller_name' => 'NotaController',
            'notas' => $notas
        ]);
    }
}
