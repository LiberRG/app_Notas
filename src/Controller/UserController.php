<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Util\SessionManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(Request $request, UserService $userService): Response
    {
        $valid = false;
        $accept = true;

        if (SessionManager::isUserLoggedIn()) {
            return $this->redirectToRoute('app_nota_list');
        } 
        
        if ($request->getMethod() === 'POST') {

            $valid = true;

            $email = $request->request->get('email');
            $pwd = $request->request->get('password');

            if (isset($email) && empty($email)) {
                $this->addFlash('warning', "El campo email es obligatorio");
                $accept = false;
            }

            if (isset($pwd) && empty($pwd)) {
                $this->addFlash('warning', "El campo contraseña es obligatorio");
                $valid = false;
                $accept = false;
            } 

            if ($accept) {
                $email = $email;
                $pwd = $pwd;
                $userResult = $userService->login($email, $pwd);
    
                if ($userResult == null) {
                    $this->addFlash('warning', "El usuario o la contraseña no son correctos, por favor intentelo de nuevo");
                    $valid = false;
                } else {
                    SessionManager::iniciarSesion();
                    $_SESSION["userId"] = $userResult->getId();
                    $_SESSION["email"] = $userResult->getEmail();
                    $_SESSION["roleId"] = $userResult->getRol();
                    $_SESSION["ultimoAcceso"] = time();
                }
            }
        }

        if ($valid) {
            $this->addFlash('success', "Se ha iniciado sesión correctamente");
            return $this->redirectToRoute('app_nota_list');
        } else {
            return $this->render(
                'user/index.html.twig',
                [
                    'page_title' => 'Iniciar sesion',
                ]
            );
        }
        
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
            $pwdconf = $request->request->get('pwdconf');
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
                if ($userService->pwdConfirmation($password, $pwdconf)) {
                    $user->setPassword(password_hash( $password, PASSWORD_BCRYPT));
                } else {
                    $this->addFlash('warning', "Las contraseñas no son iguales");
                    $valid = false;
                }
            }

            if (count($userService->list()) == 0) {
                $user->setRol(ADMIN_ROLE);
            } else {
                $user->setRol(USER_ROLE);
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
                    [
                        'page_title' => 'Crear usuario',
                        'user' => $user
                    ]
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

    #[Route('/user', name: 'app_user_list')]
    public function list(UserService $userService): Response
    {

        $users = $userService->list();
        $contador = sizeof($users);
        if ($contador > 0) {

            $this->addFlash("info", "Se han encontrado $contador notas");
        }

        return $this->render('user/list.html.twig', [
            'page_title' => 'Lista de usuarios',
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    //Se permitirá cerrar sesión en un formulario situado en el header.php que solo se mostrará si el usuario está autenticado. A su izquierda mostrará el email del usuario autenticado
    public function logout()
    {
        SessionManager::cerrarSesion();
        return $this->redirectToRoute('app_user');
    }


    // En función del rol seleccionado en login, el usuario deberá ser redirigido a:
    private function redirectAccordingToRole(UserService $userService) {
        $pagName = 'app_nota_list';
        $user_selected_rol = $userService->getRoleById($_SESSION["roleId"]);
        if ($user_selected_rol->getName() === ADMIN_ROLE) {
            $pagName = 'app_nota_list';
        } elseif ($user_selected_rol->getName() === USER_ROLE) {
            $pagName = 'app_nota_list';
        }
        return $pagName;
    }




}
