<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Socialite;
class AuthSocCtrl extends Controller
{
    
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
        return \Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        
        $user = Socialite::driver('facebook')->user();
        // OAuth One Providers
        // $token = $user->token;
        // $tokenSecret = $user->tokenSecret;

        // All Providers
        // $user->getId();
        // $user->getNickname();
        // $user->getName();
        // $user->getEmail();
        // $user->getAvatar();
        dd($user->getNickname());
    }
}
