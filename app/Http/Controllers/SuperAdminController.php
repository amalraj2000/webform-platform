<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Form;
use App\Models\Submission;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $accounts = Account::withCount('forms')->get();
        $totalSubmissions = Submission::count();

        return view('superadmin.dashboard', compact('accounts', 'totalSubmissions'));
    }

    public function showAccount(Account $account)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $account->load('forms');
        return view('superadmin.account', compact('account'));
    }

    public function showForm(Form $form)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $form->load('publishedVersion.submissions');
        return view('superadmin.form', compact('form'));
    }
}
