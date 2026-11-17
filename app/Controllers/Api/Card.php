<?php

namespace App\Controllers\Api;

use App\Models\CardModel;
use App\Services\FareCardService;
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

    public function create(){
        # create certificate (and keypair? - yes) for the new card
        # this endpoint is meant to be used by administrative users so they need to pass who the card will belong to
        $client_id = $this->request->getPost("client_id");
        $valid_for = $this->request->getPost("valid_for");

        if(empty($client_id) or empty($valid_for) or !is_numeric($client_id) or !is_numeric($valid_for)){
            $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            $this->response->setJSON([
                "status" => "error",
                "message" => "client_id and valid_for are required and have to be numeric"
            ]);

            return $this->response;
        }

        $service = new FareCardService($client_id);
        [$card_privkey, $card_pubkey] = $service->generateKeys();

        $new_card = [
            "client_id" => $client_id,
            "fingerprint" => $service->sshFingerprint($card_pubkey)
        ];

        $model = new CardModel();
        try {
            $model->insert($new_card);
            $card_id = $model->db->insertID();

        } catch(\ReflectionException $e) {
            $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            $this->response->setJSON([
                "status" => "save_err",
            ]);

            return $this->response;
        }

        $cert = $service->issueCard($card_pubkey, $valid_for, $card_id);

        $this->response->setStatusCode(ResponseInterface::HTTP_CREATED);
        $this->response->setJSON([
            "status" => "success",
            "data" => [
                "card_id" => $card_id,
                "card_pubkey" => base64_encode($card_pubkey->toString("OpenSSH")),
                "card_privkey" => base64_encode($card_privkey->toString("OpenSSH")),
                "card_cert" => base64_encode($cert),
            ]
        ]);

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
