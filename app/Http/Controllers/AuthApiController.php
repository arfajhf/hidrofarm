<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'password' => ['required', 'string', 'min:8'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ]);

        $token = Str::random(64);
        $oneTapToken = Str::random(64);
        $expiresAt = now()->addDay();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $this->emailFromName($validated['name']),
            'password' => $validated['password'],
            'profile_photo' => $this->storeProfilePhoto($request),
            'phone_number' => $validated['phone_number'] ?? null,
            'api_token_hash' => hash('sha256', $token),
            'api_token_expires_at' => $expiresAt,
            'one_tap_token_hash' => hash('sha256', $oneTapToken),
            'one_tap_expires_at' => $expiresAt,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'token_expires_at' => $expiresAt,
            'one_tap_token' => $oneTapToken,
            'one_tap_expires_at' => $expiresAt,
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('name', $validated['name'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'name' => ['Nama atau password tidak sesuai.'],
            ]);
        }

        $token = Str::random(64);
        $oneTapToken = Str::random(64);
        $expiresAt = now()->addDay();
        $user->forceFill([
            'api_token_hash' => hash('sha256', $token),
            'api_token_expires_at' => $expiresAt,
            'one_tap_token_hash' => hash('sha256', $oneTapToken),
            'one_tap_expires_at' => $expiresAt,
        ])->save();

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'token_expires_at' => $expiresAt,
            'one_tap_token' => $oneTapToken,
            'one_tap_expires_at' => $expiresAt,
            'user' => $this->serializeUser($user),
        ]);
    }

    public function oneTapLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'one_tap_token' => ['required', 'string'],
        ]);

        $user = User::where('one_tap_token_hash', hash('sha256', $validated['one_tap_token']))
            ->where('one_tap_expires_at', '>', now())
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Riwayat login sudah kedaluwarsa.'], 401);
        }

        $token = Str::random(64);
        $oneTapToken = Str::random(64);
        $expiresAt = now()->addDay();

        $user->forceFill([
            'api_token_hash' => hash('sha256', $token),
            'api_token_expires_at' => $expiresAt,
            'one_tap_token_hash' => hash('sha256', $oneTapToken),
            'one_tap_expires_at' => $expiresAt,
        ])->save();

        return response()->json([
            'message' => 'Login cepat berhasil.',
            'token' => $token,
            'token_expires_at' => $expiresAt,
            'one_tap_token' => $oneTapToken,
            'one_tap_expires_at' => $expiresAt,
            'user' => $this->serializeUser($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->userFromBearerToken($request);

        if (! $user) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        return response()->json([
            'user' => $this->serializeUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $this->userFromBearerToken($request);

        if ($user) {
            $user->forceFill([
                'api_token_hash' => null,
                'api_token_expires_at' => null,
            ])->save();
        }

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    private function userFromBearerToken(Request $request): ?User
    {
        $token = $request->bearerToken();

        if (! $token) {
            return null;
        }

        return User::where('api_token_hash', hash('sha256', $token))
            ->where('api_token_expires_at', '>', now())
            ->first();
    }

    private function storeProfilePhoto(Request $request): ?string
    {
        if (! $request->hasFile('profile_photo')) {
            return null;
        }

        $file = $request->file('profile_photo');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $directory = 'uploads/profile-photos';

        $file->move(public_path($directory), $filename);

        return $directory.'/'.$filename;
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'profile_photo' => $user->profile_photo,
            'profile_photo_url' => $user->profile_photo ? asset($user->profile_photo) : null,
            'phone_number' => $user->phone_number,
            'created_at' => $user->created_at,
        ];
    }

    private function emailFromName(string $name): string
    {
        $slug = Str::slug($name) ?: 'user';

        return $slug.'-'.Str::lower(Str::random(8)).'@smartfarm.local';
    }
}
