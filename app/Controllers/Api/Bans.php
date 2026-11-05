<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BanModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Bans extends ResourceController
{
    public function index()
    {
        $model = new BanModel();

        $bans = $model->findAll();
        if(!empty($bans)){
            if($this->request->getGet("output_type") !== null and $this->request->getGet("output_type") == "list") {
                $list = [];

                foreach($bans as $ban) {
                    $list[] = $ban->client_fingerprint;
                }

                $this->response->setJSON(ResponseInterface::HTTP_OK);
                $this->response->setJSON([
                    "status" => "success",
                    "data" => $list
                ]);
            }
            else{
                $this->response->setJSON(ResponseInterface::HTTP_OK);
                $this->response->setJSON([
                    "status" => "success",
                    "data" => $bans
                ]);
            }
        }
        else{
            $this->response->setStatusCode(ResponseInterface::HTTP_OK);
            $this->response->setJSON([
                "status" => "nodata",
                "message" => "No bans found"
            ]);
        }

        return $this->response;
    }
}
