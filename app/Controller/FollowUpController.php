<?php

namespace Assessment\Controller;

use Assessment\Core\Auth;
use Assessment\Core\Request;
use Assessment\Core\Response;
use Assessment\Service\FollowupService;

class FollowUpController
{
    protected FollowupService  $followupService; protected Auth $auth;

    public function __construct(FollowupService $followupService, Auth $auth)
    {
        $this->auth = $auth;
        $this->followupService = $followupService;
    }

    public function create(Request $request)
    {
        $user = $this->auth->user($request);
        $followUpId = $this->followupService->create($request->body(),$user);

        return Response::json([
            'message' => "Follow Up Created Successfully",
            'data' => [$request->body()],
            'id' => $followUpId
        ],201);
    }

    public function index(Request $request): Response
    {
        $data = [
            'page' => $request->query('page'),
            'per_page' => $request->query('per_page'),
        ];

        return Response::json([
            'message' => 'Show Successfully',
            'page' => $data['page'],
            'per_page' => $data['per_page'],
            'data' => $this->followupService->index($data),
            'status' => 'success',
        ], 200);
    }

    public function show(Request $request,$params){
        $id = (int) $params['id'];

        return Response::json([
            'message' => 'Show Successfully',
            'data' => [$this->followupService->show($id)],
            'status' => 'success',
        ],200);
    }

    public function update(Request $request,$params)
    {
        $user = $this->auth->user($request);
        $followUpId = (int) $params['id'];

        return Response::json([
            'message' => 'Updated Successfully',
            'data' => [$this->followupService->update($followUpId,$request->body(),$user)],
            'status' => 'success'
        ],200);
    }
}