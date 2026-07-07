<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'exists' => 'El :attribute seleccionado es inválido.',
    'image' => 'El archivo debe ser una imagen.',
    'mimes' => 'El archivo debe ser un archivo de tipo: :values.',
    'max' => [
        'array' => 'El campo :attribute no debe contener más de :max elementos.',
        'file' => 'El archivo no debe pesar más de :max kilobytes.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'request_type_id' => 'tipo de trámite',
        'address' => 'dirección',
        'description' => 'descripción',
        'tree_id' => 'árbol',
        'foto' => 'foto',
    ],
];
