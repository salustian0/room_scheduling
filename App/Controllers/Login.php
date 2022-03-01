<?php
namespace App\Controllers;

use App\Entity\UserEntity;
use App\Models\UserModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Session;
use App\Views\View;

class Login{

    /**
     * Tela de login
     */
    static function login(){
        if(Session::verifySession()){
            return (new Response())->redirect('/home');
        }
        $view = new View();
        $vars = [];
        $oldData = Session::getFlashData('oldData');
        if (!empty($oldData)) {
            $vars['_data'] = $oldData;
        }

        $view->render('user/login',$vars);
    }

    static function auth(Request $request){
        $response = new Response();
        /**
         * Setando usuário e senha na entidade
         */
        $userEntity = new UserEntity();
        $userEntity->setUsername($request->getPostParams('username'));
        $userEntity->setPassword($request->getPostParams('password'));

        $oldData = array(
            'username' => $userEntity->getUsername(),
            'password' => $userEntity->getPassword()
        );

        /**
         * Validação do login
         */
        $errors = self::validateAuth($userEntity);
        if(!empty($errors)){
            $message = array('error' => $errors);
            return $response->redirect('/login', $message,$oldData);
        }

        $userModel = new UserModel();
        $dbUserEntity = $userModel->getUserByUsername($userEntity->getUsername());

        if(empty($dbUserEntity)){
            $message = array('error' => array('Usuário ou senha inválido(s)'));
            return $response->redirect('/login', $message, $oldData);
        }

        if(password_verify($userEntity->getPassword(), $dbUserEntity->getPassword())){

            $session = array(
                'id' =>  $dbUserEntity->getId(),
                'username' => $dbUserEntity->getUsername(),
                'access' => $dbUserEntity->getAccess()
            );

            Session::setSession('_USER', $session);
            return $response->redirect('/home');
        }

        $message = array('error' => array('Usuário ou senha inválido(s)'));
        return $response->redirect('/login', $message, $oldData);
    }


    /**
     * @param UserEntity $entity
     * @return array
     */
    private static function validateAuth(UserEntity  $entity) : array{
        if(Session::verifySession()){
            return (new Response())->redirect('/home');
        }
        $errors = array();
        if(empty($entity->getUsername())){
            array_push($errors, 'É necessário informar o usuário para prosseguir com o login');
        }

        if(empty($entity->getPassword())){
            array_push($errors, 'É necessário informar a senha para prosseguir o login');
        }
        return $errors;
    }

    /**
     * Desloga
     */
    static function logout(){
        session_destroy();
        return (new Response())->redirect('/login');
    }

    static function verifyAuth(){
        if(!Session::verifySession()){
            (new Response())->redirect('/login');
        }
    }
}