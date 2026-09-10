<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Form;
use App\Models\Submission;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $accounts = Account::withCount('forms')->get();
        $totalSubmissions = Submission::count();

        return view('superadmin.dashboard', compact('accounts', 'totalSubmissions'));
    }

    public function accounts()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $accounts = Account::withCount('forms')->get();

        return view('superadmin.account', compact('accounts'));
    }

    public function showAccount(Account $account)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $accounts = Account::withCount('forms')->get();
        $account->load('forms');

        return view('superadmin.account', compact('accounts', 'account'));
    }

    public function showForm(Form $form)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        $accounts = Account::withCount('forms')->get();
        $form->load('publishedVersion.submissions');
        $account = $form->account;

        return view('superadmin.form', compact('accounts', 'account', 'form'));
    }
}
