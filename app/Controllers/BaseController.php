<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function authUser(): ?array
    {
        $user = service('session')->get('auth_user');
        return is_array($user) ? $user : null;
    }

    protected function authAllows(array $roles = []): bool
    {
        $user = $this->authUser();
        if ($user === null) {
            return false;
        }

        return $roles === [] || in_array((string) ($user['role'] ?? ''), $roles, true);
    }
}
