<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
class Pubkey extends BaseController
{
    public function __invoke(): ResponseInterface{
        $key = env('PUBLIC_KEY_BASE64');

        if(!empty($key)){
            $this->response->setStatusCode(ResponseInterface::HTTP_OK);
            $this->response->setJSON(['status' => 'success', 'data' => $key]);
        }
        else{
            $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            $this->response->setJSON(['status' => 'error', 'message' => 'Public key not found']);
        }

        return $this->response;
    }
}
