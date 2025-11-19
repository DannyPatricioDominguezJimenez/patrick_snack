<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que lleguen los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // 3. Verificar si el usuario existe y la contraseña es correcta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 4. Verificar si la cuenta está habilitada (Tu lógica original)
        if (!$user->enabled) {
            return response()->json([
                'message' => 'Su cuenta está deshabilitada. Contacte al administrador.'
            ], 403); // 403 Forbidden
        }

        // 5. Crear el token (Borramos tokens viejos para no acumular basura)
        $user->tokens()->delete();
        $token = $user->createToken('flutter-app')->plainTextToken;

        // 6. Respuesta a Flutter
        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->rol // Si tienes roles, es útil enviarlo aquí
            ]
        ], 200);
    }
    
    public function logout(Request $request) {
        // Revoca el token actual
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}