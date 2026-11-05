<?php

namespace App\Controllers\Api;

use App\Entities\LogEntry;
use App\Models\LogModel;
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

    private function insertBatch(array $batch, $model): bool{
        if(empty($batch)){
            return false;
        }

        try {
            return $model->insertBatch($batch);
        } catch (\ReflectionException) {
            return false;
        }
    }

    public function store(){
        $incoming_json = $this->request->getJSON(true);
        $data = $incoming_json["data"];
        $bulk = $incoming_json["bulk"];
        $gate_id = $this->request->header("gate_id")->getValue();

        $model = new LogModel();

        if(empty($incoming_json)){
            return $this->fail("No data provided");
        }

        if($bulk) {
            $batch = [];
            $created_ids = [];
            $failed_ids = [];
            $duplicate_ids = [];

            # processing batch
            foreach($data as $entry){
                $new_entry = new LogEntry();
                $new_entry->id = $entry["id"];
                $new_entry->gate_id = $gate_id;
                $new_entry->client_fingerprint = $entry["client_fingerprint"];
                $new_entry->action_type = $entry["action_type"];
                $new_entry->action_date = $entry["created_at"];

                if($model->find($new_entry->id) !== null or in_array($new_entry->id, $created_ids)){
                    $duplicate_ids[] = $new_entry->id;
                    continue;
                }

                if($model->validate($new_entry->toRawArray())){
                    $created_ids[] = $new_entry->id;
                    $batch[] = $new_entry;
                }
                else{
                    $failed_ids[] = $entry["id"];
                }
            }

            # OK
            if (!empty($batch) and empty($duplicate_ids) and empty($failed_ids)){
                if($this->insertBatch($batch, $model)){
                    $this->response->setStatusCode(ResponseInterface::HTTP_CREATED);
                    $this->response->setJSON([
                        "status" => "success",
                    ]);
                }
            }
            # DUPLICATES
            else if (!empty($duplicate_ids) and empty($failed_ids) and empty($batch)) {
                $this->response->setStatusCode(ResponseInterface::HTTP_CONFLICT);
                $this->response->setJSON([
                    "status" => "duplicates",
                    "duplicates" => $duplicate_ids
                ]);
            }
            # SOME FAILED SOME SAVED and/or SOME DUPLICATES
            else if ((!empty($duplicate_ids) + !empty($failed_ids) + !empty($created_ids)) >= 2) {
                if($this->insertBatch($batch, $model)){
                    $this->response->setStatusCode(ResponseInterface::HTTP_MULTI_STATUS);
                    $this->response->setJSON([
                        "status" => "partial_success",
                        "created" => $created_ids,
                        "duplicates" => $duplicate_ids,
                        "failed" => $failed_ids,
                    ]);
                }
                else if(!empty($batch)){
                    $this->response->setStatusCode(ResponseInterface::HTTP_MULTI_STATUS);
                    $this->response->setJSON([
                        "status" => "partial_success",
                        "created" => $created_ids,
                        "duplicates" => $duplicate_ids,
                        "failed" => $failed_ids,
                    ]);
                }
                else if(!empty($duplicate_ids) and !empty($failed_ids)){
                    $this->response->setStatusCode(ResponseInterface::HTTP_MULTI_STATUS);
                    $this->response->setJSON([
                        "status" => "partial_success",
                        "created" => $created_ids,
                        "duplicates" => $duplicate_ids,
                        "failed" => $failed_ids,
                    ]);
                }
                else{
                    $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                    $this->response->setJSON([
                        "status" => "save_err",
                        "errors" => $model->errors()
                    ]);
                }
            }
            # ALL FAILED
            else{
                $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
                $this->response->setJSON([
                    "status" => "val_err",
                    "failed" => $failed_ids
                ]);
            }
        }
        else{
            $new_entry = new LogEntry();
            $new_entry->id = $data["id"];
            $new_entry->gate_id = $gate_id;
            $new_entry->client_fingerprint = $data["client_fingerprint"];
            $new_entry->action_type = $data["action_type"];
            $new_entry->action_date = $data["created_at"];

            if($model->validate($new_entry->toRawArray())){
                try {
                    $model->insert($new_entry);

                    $this->response->setStatusCode(ResponseInterface::HTTP_CREATED);
                    $this->response->setJSON([
                        "status" => "success"
                    ]);
                } catch(\ReflectionException){
                    $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                    $this->response->setJSON([
                        "status" => "save_err"
                    ]);
                }
            }
            else{
                $this->response->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
                $this->response->setJSON([
                    "status" => "val_err",
                    "errors" => $model->errors()
                ]);
            }
        }

        return $this->response;
    }
}
