<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService{

    //CTRL+ALT+C (ext. class helper)
	public function __construct(private EntityManagerInterface $em, 
    private UserRepository $userRepository)
	{
	}

    public function create(User $user):User{
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function list():array{
       $users = $this->userRepository->findAll();
        return $users;
    }
    public function findById($id){
        $user = $this->userRepository->find($id);
        return $user;
    }

    public function update($user){
        $this->em->flush($user);
        return $user;
    }

    public function delete($user){
        $this->em->remove($user);
        $this->em->flush();
    }
}