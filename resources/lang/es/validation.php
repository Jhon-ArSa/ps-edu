<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena.',
    'email' => 'El campo :attribute debe ser un correo valido.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file' => 'El archivo :attribute debe tener al menos :min kilobytes.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe exceder :max caracteres.',
        'numeric' => 'El campo :attribute no debe exceder :max.',
        'file' => 'El archivo :attribute no debe exceder :max kilobytes.',
        'array' => 'El campo :attribute no debe exceder :max elementos.',
    ],
    'confirmed' => 'La confirmacion de :attribute no coincide.',
    'unique' => 'El campo :attribute ya existe.',
    'in' => 'El campo :attribute tiene un valor invalido.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'integer' => 'El campo :attribute debe ser un numero entero.',
    'numeric' => 'El campo :attribute debe ser numerico.',

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo',
        'dni' => 'dni',
        'phone' => 'telefono',
        'password' => 'contrasena',
        'password_confirmation' => 'confirmacion de contrasena',
        'role' => 'rol',
        'status' => 'estado',
        'code' => 'codigo',
        'promotion_year' => 'anio de promocion',
        'program' => 'programa',
        'title' => 'titulo',
        'degree' => 'grado',
        'specialty' => 'especialidad',
        'category' => 'categoria',
        'years_of_service' => 'anios de servicio',
    ],
];
