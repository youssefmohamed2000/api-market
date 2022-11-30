<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['logout', 'sendEmailVerification', 'verify']);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->safe();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        event(new Registered($user));
        $data['user'] = $user;
        $data['token'] = $user->createToken('MyAuthApp')->plainTextToken;

        return $this->sendResponse($data, 'User Registered successfully.');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->safe();
        if (auth()->attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $data['user'] = auth()->user();
            $data['token'] = auth()->user()->createToken('MyAuthApp')->plainTextToken;
            return $this->sendResponse($data, 'User Signed in');
        }
        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }

    public function logout()
    {
        //auth()->user()->currentAccessToken()->delete();
        auth()->user()->tokens()->delete();
        return $this->sendResponse([], 'Logged Out Successfully');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->safe();
        $status = Password::sendResetLink([
                'email' => $validated['email']
            ]
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->sendResponse([], $status)
            : $this->sendError([], $status);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->safe()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->sendResponse([], $status)
            : $this->sendError([], $status);
    }

    public function sendEmailVerification()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->sendResponse([], 'Email already verified');
        }
        auth()->user()->sendEmailVerificationNotification();
        return $this->sendResponse([], 'Verification Email Sent');
    }

    public function verify()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->sendResponse([], 'Email already verified');
        }

        if (auth()->user()->markEmailAsVerified()) {
            event(new Verified(auth()->user()));
        }

        return $this->sendResponse([], 'Email has been verified');
    }

}
