<?php

namespace Botble\JobBoard\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\ResetsPasswords;
use Botble\JobBoard\Models\Account;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    public function showResetForm(Request $request, $token = null)
    {
        SeoHelper::setTitle(__('Reset Password'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(
            __('Reset Password'),
            route('public.account.register')
        );

        return Theme::scope(
            'job-board.auth.passwords.reset',
            ['token' => $token, 'email' => $request->input('email')],
            'plugins/job-board::themes.auth.passwords.reset'
        )->render();
    }

    public function redirectTo()
    {
        /**
         * @var Account $account
         */
        $account = request()->user('account');

        if (! $account->isEmployer()) {
            $this->redirectTo = route('public.index');
        }

        return $this->redirectTo;
    }

    public function broker()
    {
        return Password::broker('accounts');
    }

    protected function guard()
    {
        return auth('account');
    }
}
