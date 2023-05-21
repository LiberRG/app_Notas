<?php
namespace App\Service;

use App\Entity\Nota;
use App\Repository\NotaRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotaService{

    //CTRL+ALT+C (ext. class helper)
	public function __construct(private EntityManagerInterface $em, 
    private NotaRepository $notaRepository)
	{
	}

    public function create(Nota $nota):Nota{
        $this->em->persist($nota);
        $this->em->flush();

        return $nota;
    }

    public function list():array{
       $notas = $this->notaRepository->findAll();
        return $notas;
    }
    public function findById($id){
        $nota = $this->notaRepository->find($id);
        return $nota;
    }

    public function update($nota){
        $this->em->flush($nota);
        return $nota;
    }

    public function delete($nota){
        $this->em->remove($nota);
        $this->em->flush();
    }
    
    // public function delete($nota, $confirmación){
    //     $nota = $this->notaRepository->remove($nota, $confirmación);
    //     return $nota;
    // }
}