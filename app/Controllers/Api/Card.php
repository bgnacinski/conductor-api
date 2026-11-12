<?php

namespace App\Controllers\Api;

use App\Models\CardModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Card extends ResourceController
{
    # this should list all fare cards - requires more advanced access control
    public function index($banned_only = false): ResponseInterface{
        $model = new CardModel();
        $newer_than = (!empty($this->request->getGet("newer_than")) and strtotime($this->request->getGet("newer_than")))
            ?$this->request->getGet("newer_than") : null;
        $is_list = $this->request->getGet("output_type") === "list";

        if ($banned_only) {
            $resp_data = $model->getBanned($newer_than);

            if ($is_list and $newer_than) {
                $banned = [];
                $unbanned = [];
                foreach ($resp_data as $item) {
                    if($item->deleted_at){
                        $unbanned[] = $item->fingerprint;
                    }
                    else {
                        $banned[] = $item->fingerprint;
                    }
                }

                $resp_data = [
                    "banned" => $banned,
                    "unbanned" => $unbanned
                ];
            }
            else if($is_list){
                $tmp = [];
                foreach ($resp_data as $item) {
                    $tmp[] = $item->fingerprint;
                }

                $resp_data = $tmp;
            }
        } else {
            if($newer_than){
                $resp_data = $model->where("created_at >", $newer_than)->where("deleted_at IS NULL")->findAll();
            }
            else{
                $resp_data = $model->where("deleted_at IS NULL")->findAll();
            }
        }

        $this->response->setStatusCode(ResponseInterface::HTTP_OK);

        if($resp_data){
            $this->response->setJSON([
                "status" => "success",
                "data" => $resp_data
            ]);
        }
        else{
            $this->response->setJSON([
                "status" => "nodata"
            ]);
        }

        return $this->response;

    }

    # this should show data about requested fare card - requires more advanced access control
   public function show($id = null): ResponseInterface{
        if(empty($id)){
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $model = new CardModel();
        $result = $model->find($id);

        if($result){
            $this->response->setStatusCode(ResponseInterface::HTTP_OK);
            $this->response->setJSON([
                "status" => "success",
                "data" => $result
            ]);
        }
        else{
            $this->response->setStatusCode(ResponseInterface::HTTP_OK);
            $this->response->setJSON([
                "status" => "nodata"
            ]);
        }

        return $this->response;
   }

    #returns CA's pubkey
    public function ca_pubkey(): ResponseInterface{
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
