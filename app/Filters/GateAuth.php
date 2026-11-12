<?php

namespace App\Filters;

use App\Models\GateModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth_header = $request->header('Authorization');
        try {
            $header_val = $auth_header->getValue();
            $auth_key = explode(' ', $header_val, 3)[1];
        }
        catch (\Throwable){
            $response = response()->setStatusCode(403);
            $response->setJSON(['error' => 'Unauthorized']);

            return $response;
        }

        $gate_model = new GateModel();
        $gate = $gate_model->where('auth_key', $auth_key)->first();

        if($gate){
            $request->appendHeader('gate_id', $gate['id']);
            return $request;
        }
        else{
            $response = response()->setStatusCode(403);
            $response->setJSON(['error' => 'Unauthorized']);

            return $response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
