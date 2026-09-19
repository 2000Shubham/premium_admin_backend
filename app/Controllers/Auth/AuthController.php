<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\LoginModel;

class AuthController extends BaseController
{
    protected $format = 'json';

    public function login()
    {
        try {
            $requestData = $this->request->getJSON(true);
            if (! $requestData) {
                $requestData = $this->request->getPost();
            }
            if (! is_array($requestData)) {
                $requestData = [];
            }

            $email    = $this->payloadValue($requestData, ['Email', 'email', 'UserName', 'username']);
            $password = $this->payloadValue($requestData, ['Password', 'password']);

            if ($email === null || $email === '') {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Email is required',
                        'res_massage' => 'Email is required',
                    ]);
            }
            if ($password === null || $password === '') {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'res_code'    => 400,
                        'status'      => false,
                        'message'     => 'Password is required',
                        'res_massage' => 'Password is required',
                    ]);
            }

            $result = (new LoginModel())->login([
                'Email'    => $email,
                'Password' => $password,
            ]);

            return $this->response->setStatusCode(200)
                ->setJSON([
                    'res_code'    => 200,
                    'status'      => true,
                    'message'     => 'Login successful',
                    'res_massage' => 'Login successful',
                    'data'        => $result,
                ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (preg_match('/\[SQL Server\](.*)$/s', $message, $matches)) {
                $message = trim($matches[1]);
            }

            $code = 400;
            if (stripos($message, 'Invalid email or password') !== false) {
                $code = 401;
            } elseif (stripos($message, 'inactive') !== false) {
                $code = 403;
            }

            return $this->response->setStatusCode($code)
                ->setJSON([
                    'res_code'    => $code,
                    'status'      => false,
                    'message'     => $message,
                    'res_massage' => $message,
                ]);
        }
    }

    private function payloadValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== '' && $data[$key] !== null) {
                return $data[$key];
            }
        }

        return $default;
    }
}
