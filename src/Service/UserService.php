<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Rol;
use App\Repository\UserRepository;
use App\Repository\RolRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private RolRepository $rolRepository;

    //CTRL+ALT+C (ext. class helper)
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    ) {
    }

    public function create(User $user): User
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function list(): array
    {
        $users = $this->userRepository->findAll();
        return $users;
    }
    public function findById($id)
    {
        $user = $this->userRepository->find($id);
        return $user;
    }
    public function findRolByName($name)
    {
        $rol = $this->rolRepository->findOneBy(['name' => $name]);
        return $rol;
    }

    public function update($user)
    {
        $this->em->flush($user);
        return $user;
    }

    public function delete($user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function getRoleById(int $roleId): Rol {

        $roles = $this->rolRepository->getRoles();
        foreach ($roles as $rol) {
            if ($rol->getId() === $roleId) {
                return $rol;
            }
        }


        return null;
    }

    public function login(string $user, string $pwd): ?User {

        $userResult = $this->userRepository->findOneBy(['email' => $user]);

        if ($userResult != null && $pwd = $userResult->getPassword()) {
            return $userResult;
        }
        //Si guardamos la contaseña con hash

        // if ($userResult != null && password_verify($pwd, $userResult->getPassword())) {
        //     return $userResult;
        // }
        
        return null;
    }



    static function pwdConfirmation($pwd, $pwdconf)
    {
        return ($pwd == $pwdconf);
    }
}
