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
}