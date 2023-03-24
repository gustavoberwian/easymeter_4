<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\LoginModel;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Traits\Viewable;



/**
 * Handles "Magic Link" logins - an email-based
 * no-password login protocol. This works much
 * like password reset would, but Shield provides
 * this in place of password reset. It can also
 * be used on it's own without an email/password
 * login strategy.
 */
class MagicLinkController extends BaseController
{

    public function __construct()
    {
        $this->input = \Config\Services::request();
    }


    public function verify()
    {
        $token = $this->request->getGet('token');

        /** @var UserIdentityModel $identityModel */
        $identityModel = model(UserIdentityModel::class);

        $identity = $identityModel->getIdentityBySecret(Session::ID_TYPE_MAGIC_LINK, $token);

        $identifier = $token ?? '';

        // No token found?
        if ($identity === null) {
            $this->recordLoginAttempt($identifier, false);

            $credentials = ['magicLinkToken' => $token];
            Events::trigger('failedLogin', $credentials);

            return redirect()->route('magic-link')->with('error', lang('Auth.magicTokenNotFound'));
        }

        // Delete the db entry so it cannot be used again.
        $identityModel->delete($identity->id);

        // Token expired?
        if (Time::now()->isAfter($identity->expires)) {
            $this->recordLoginAttempt($identifier, false);

            $credentials = ['magicLinkToken' => $token];
            Events::trigger('failedLogin', $credentials);

            return redirect()->route('magic-link')->with('error', lang('Auth.magicLinkExpired'));
        }

        /** @var Session $authenticator */
       // $authenticator = auth('session')->getAuthenticator();

        // Log the user in
      //  $authenticator->loginById($identity->user_id);

      //  $user = $authenticator->getUser();

      //  $this->recordLoginAttempt($identifier, true, $user->id);

        // Give the developer a way to know the user
        // logged in via a magic link.
      //  session()->setTempdata('magicLogin', true);

     //   Events::trigger('magicLogin');

        // Get our login redirect url
         
        $data['user'] = $identity;
 
        return view('update_password', $data);
    }

    /**
     * @param int|string|null $userId
     */
    private function recordLoginAttempt(
        string $identifier,
        bool $success,
        $userId = null
    ): void {
        /** @var LoginModel $loginModel */
        $loginModel = model(LoginModel::class);

        $loginModel->recordLoginAttempt(
            Session::ID_TYPE_MAGIC_LINK,
            $identifier,
            $success,
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent(),
            $userId
        );
    }

    /**
     * Returns the rules that should be used for validation.
     *
     * @return array<string, array<string, string>>
     */
    protected function getValidationRules(): array
    {
        return [
            'email' => [
                'label' => 'Auth.email',
                'rules' => config('AuthSession')->emailValidationRules,
            ],
        ];
    }



    public function updateP(){
        /** @var Passwords $passwords */
        $passwords = service('passwords');
       $credentials = array('confirm_password' => $this->input->getPost('confirm_password'), );

        $db = \Config\Database::connect();
        $identityModel =  $db->table('auth_identities');
        $identityModel->set('secret2',$passwords -> hash($credentials['confirm_password']));
        $identityModel->where('user_id', $this->input->getPost('user_id'));
        $identityModel->where('type', 'email_password');
        $identityModel->update();
        return redirect()->route('/');
     }
}
