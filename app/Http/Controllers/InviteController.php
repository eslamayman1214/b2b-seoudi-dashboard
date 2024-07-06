<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\InviteRequest;
use App\Services\InviteService;

class InviteController extends Controller
{
    protected $inviteService;
    protected $logService;

    public function __construct(InviteService $inviteService, LogHelper $logService)
    {
        $this->inviteService = $inviteService;
        $this->logService = $logService;
    }

    public function create()
    {
        $this->logService->logAction('View Invite Page', 'Invite page viewed.');
        return $this->inviteService->showInvitePage();
    }

    public function send(InviteRequest $request)
    {
        $this->logService->logAction('Invite User', "Invited user: {$request->email}");
        return $this->inviteService->sendInvite($request);
    }
}