<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Log extends ResourceController
{
    use ResponseTrait;

    public function index()
    {
        //
    }

    public function store(){
        echo $this->request->header("gate_id");
    }
}
