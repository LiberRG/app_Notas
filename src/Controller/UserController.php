<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        //Falta validación usuario
        return $this->render('user/index.html.twig', [
            'page_title' => 'Iniciar sesión',
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/new/{id<([1-9]+\d*)>}', name: 'app_user_new')]
    public function crear(Request $request, UserService $userService, int $id = null): Response
    {
        $user = new User();
        $valid = false;
        $update = false;

        if ($request->getMethod() === 'POST') {

            $valid = true;
            if ($id != null) {
                $update = true;
                $user = $userService->findById($id);
            }
            //Se usa $request->request para obtener variables enviadas por POST
            //Se usa $request->query para obtener variables enviadas por GET
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $pwdconf= $request->request->get('pwdconf');
            // $rol =  $request->request->get('rol');

            if (isset($email) && empty($email)) {
                $this->addFlash('warning', "El campo email es obligatorio");
                $valid = false;
            } else {
                $user->setEmail($email);
            }
            
            if (isset($password) && empty($password)) {
                $this->addFlash('warning', "El campo contraseña es obligatorio");
                $valid = false;
            } else {
                if ($userService->pwdConfirmation($password, $pwdconf)){$user->setPassword($password);
                }else{
                    $this->addFlash('warning', "Las contraseñas no son iguales");
                    $valid = false;
                }
            }

            if (count($userService->list()) == 0 ) {
                $user->setRol(\ADMIN_ROLE);
            } else {
                $user->setRol(\USER_ROLE);
            }
        }

        if ($valid) {
            if ($update) {
                $user = $userService->update($user);
                $this->addFlash('success', "Se ha actualizado el usuario correctamente");
            } else {
                $user = $userService->create($user);
                $this->addFlash('success', "Se ha creado el usuario correctamente");
            }
            return $this->redirectToRoute('app_nota_list');
        } else {
            if ($update) {
                return $this->redirectToRoute('app_user_update', ["id" => $id]);
            } else {
                return $this->render(
                    'user/crear.html.twig',
                    ['page_title' => 'Crear usuario',
                    'user' => $user]
                );
            }
        }
    }


    #[Route('/user/update/{id<([1-9]+\d*)>}', name: 'app_user_update')]
    public function update(UserService $userService, int $id): Response
    {
        $user = $userService->findById($id);
        if ($user == null) {
            throw   $this->createNotFoundException();
        }
        return $this->render('user/editar.html.twig', [
            'page_title' => 'Editar Usuario',
            'controller_name' => 'userController',
            'user' => $user,
        ]);
    }

}
