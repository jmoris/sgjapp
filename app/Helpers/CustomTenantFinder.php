<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class CustomTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    public function findForRequest(Request $request):?Tenant
    {
        if ($request->hasCookie('tenant')) {
            $accountId = $request->cookie('tenant');

            $accountId = ($accountId);

            $account = $this->getTenantModel()::find($accountId);

            if (!empty($account)) {
                return $account;
            }

            Cookie::forget('tenant');
        }
        return null;
    }
}
