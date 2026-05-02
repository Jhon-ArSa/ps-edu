<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Lista de contraseñas comunes prohibidas
     */
    private const COMMON_PASSWORDS = [
        'password', 'Password123', '12345678', 'qwerty123', 'abc123456',
        'password1', 'Password1', '123456789', 'admin123', 'Admin123',
        'letmein', 'welcome', 'monkey', 'dragon', 'master',
    ];

    /**
     * Validar la contraseña
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Verificar longitud mínima
        if (strlen($value) < 8) {
            $fail('La contraseña debe tener al menos 8 caracteres.');
            return;
        }

        // Verificar que contenga al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra mayúscula.');
            return;
        }

        // Verificar que contenga al menos una letra minúscula
        if (!preg_match('/[a-z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra minúscula.');
            return;
        }

        // Verificar que contenga al menos un número
        if (!preg_match('/[0-9]/', $value)) {
            $fail('La contraseña debe contener al menos un número.');
            return;
        }

        // Verificar que contenga al menos un carácter especial
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('La contraseña debe contener al menos un carácter especial (!@#$%^&*()_+-=[]{}|;:,.<>?).');
            return;
        }

        // Verificar que no sea una contraseña común
        foreach (self::COMMON_PASSWORDS as $commonPassword) {
            if (strtolower($value) === strtolower($commonPassword)) {
                $fail('Esta contraseña es demasiado común. Por favor, elige una contraseña más segura.');
                return;
            }
        }

        // Verificar que no contenga secuencias obvias
        if (preg_match('/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $value)) {
            $fail('La contraseña no debe contener secuencias obvias (123, abc, etc.).');
            return;
        }
    }

    /**
     * Obtener el mensaje de ayuda para el usuario
     */
    public static function getHelpMessage(): string
    {
        return 'La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales.';
    }
}
