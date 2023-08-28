<?php

namespace Hrvoje\PhpFramework\Controller;

use Hrvoje\PhpFramework\Database\Connection;
use Hrvoje\PhpFramework\Exceptions\ValidationException;
use Hrvoje\PhpFramework\Model\User;
use Hrvoje\PhpFramework\Request\Request;
use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Validator\UserValidator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class UserController
{
    protected Request $request;
    protected Environment $twig;
    protected array $formData;

    /**
     * @param mixed $request
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->formData = $request->getRequestParams();

        $loader = new FilesystemLoader(getcwd()."/templates");
        $this->twig = new Environment($loader);
    }

    public function users(): Response
    {
        $users = Connection::getInstance()->select("SELECT * FROM users")->fetchAssocAll();

        return new Response($this->twig->render("users/users.html", ["users" => $users]));
    }

    public function insertUserView(): Response
    {
        return new Response($this->twig->render("users/insertUser.html", []));
    }

    public function handleInsertion(): Response
    {
        try {
            UserValidator::validate($this->formData);
        } catch (ValidationException $e) {
            return new Response($e->getMessage());
        }

        $user = new User();
        $user->first_name = $this->formData['first_name'];
        $user->last_name = $this->formData['last_name'];
        $user->dob = $this->formData['dob'];
        $user->save();

        return new Response($this->twig->render(
            "users/insertionSuccess.html",
            [
                "id" => $user->id,
                "firstName" => $user->first_name,
                "lastName" => $user->last_name,
                "dob" => $user->dob
            ]
        ));
    }

    public function updateUserView(int $userId): Response
    {
        $user = User::find($userId);

        return new Response($this->twig->render(
            "users/updateUser.html",
            [
                "id" => $user->id,
                "firstName" => $user->first_name,
                "lastName" => $user->last_name,
                "dob" => $user->dob
            ]
        ));
    }

    public function updateUser(int $userId): Response
    {
        try {
            UserValidator::validate($this->formData);
        } catch (ValidationException $e) {
            return new Response($e->getMessage());
        }

        $user = new User();
        $user->id = $userId;
        $user->first_name = $this->formData['first_name'];
        $user->last_name = $this->formData['last_name'];
        $user->dob = $this->formData['dob'];
        $user->save();

        return $this->users();
    }
}
