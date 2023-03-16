<?php
function avatar($avatar)
{
    if ($avatar == 'none') {

        return base_url('assets/img/user.png');

    } elseif  (file_exists('uploads/avatars/'.$avatar)) {

        return base_url('uploads/avatars/' . $avatar);

    } else {

        // verificar qdo mostra
        return base_url('assets/img/sistema.png');
    }


     function verify(): RedirectResponse
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
        $authenticator = auth('session')->getAuthenticator();

        // If an action has been defined
        if ($authenticator->hasAction($identity->user_id)) {
            return redirect()->route('auth-action-show')->with('error', lang('Auth.needActivate'));
        }

        // Log the user in
        $authenticator->loginById($identity->user_id);

        $user = $authenticator->getUser();

        $this->recordLoginAttempt($identifier, true, $user->id);

        // Give the developer a way to know the user
        // logged in via a magic link.
        session()->setTempdata('magicLogin', true);

        Events::trigger('magicLogin');

        // Get our login redirect url
        return redirect()->to(config('Auth')->loginRedirect());
    }

}