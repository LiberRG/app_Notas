<?php

namespace App\Controller;

use App\Entity\Nota;
use App\Service\NotaService;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(NotaService $notaService): Response
    {
        $nota = new Nota();
        $nota->setTitulo("Mi tercera nota");
        $nota->setDescripcion("Mi tercera nota con Symfony y Doctrine");

        $nota = $notaService->create($nota);


        return $this->render('nota/index.html.twig', [
            'controller_name' => 'NotaController',
            'nota' => $nota
        ]);
    }


    #[Route('/nota', name: 'app_nota_list')]
    public function list(NotaService $notaService): Response
    {
        

        $notas = $notaService->list();


        return $this->render('nota/list.html.twig', [
            'controller_name' => 'NotaController',
            'notas' => $notas
        ]);
    }


}
