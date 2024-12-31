<?php

declare(strict_types=1);

/**
 * Derafu: Biblioteca PHP (Núcleo).
 * Copyright (C) Derafu <https://www.derafu.org>
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General Affero de GNU publicada por
 * la Fundación para el Software Libre, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero SIN
 * GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o de APTITUD
 * PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de la Licencia Pública
 * General Affero de GNU para obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

return [
    'without_validation_because_empty_schema' => [
        'data' => [
            'foo' => 'bar',
        ],
        'schema' => [
            // Vacío a propósito para corroborar que funcione sin esquema.
        ],
        'expected' => [
            'foo' => 'bar',
        ],
    ],
    'basic_schema_validation_ok' => [
        'data' => [
            'name' => 'John',
        ],
        'schema' => [
            'name' => [
                'required' => true,
                'types' => 'string',
            ],
            'age' => [
                'types' => 'int',
                'default' => 18,
            ],
        ],
        'expected' => [
            'name' => 'John',
            'age' => 18,
        ],
    ],
    'basic_schema_validation_fail' => [
        'data' => [
            // Vacío a propósito para que falle.
        ],
        'schema' => [
            'email' => [
                'required' => true,
                'types' => 'string',
            ],
        ],
        'expected' => MissingOptionsException::class,
    ],
    'choices_validation_ok' => [
        'data' => [
            'status' => 'active',
        ],
        'schema' => [
            'status' => [
                'required' => true,
                'types' => 'string',
                'choices' => ['active', 'inactive'],
            ],
        ],
        'expected' => [
            'status' => 'active',
        ],
    ],
    'choices_validation_fail' => [
        'data' => [
            'status' => 'invalid',
        ],
        'schema' => [
            'status' => [
                'required' => true,
                'types' => 'string',
                'choices' => ['active', 'inactive'],
            ],
        ],
        'expected' => InvalidOptionsException::class,
    ],
    'normalizer_usage_types_ok' => [
        'data' => [
            'price' => '123.45',
        ],
        'schema' => [
            'price' => [
                'types' => ['float', 'string'],
                'normalizer' => 'float', // En realidad debería ser un closure.
            ],
        ],
        'expected' => [
            'price' => 123.45,
        ],
    ],
    'normalizer_usage_types_fail' => [
        'data' => [
            'price' => '123.45',
        ],
        'schema' => [
            'price' => [
                'types' => 'float',
                'normalizer' => 'float', // En realidad debería ser un closure.
            ],
        ],
        'expected' => InvalidOptionsException::class,
    ],
    'nested_schema_validation' => [
        'data' => [
            'user' => [
                'name' => 'John',
            ],
        ],
        'schema' => [
            'user' => [
                'types' => 'array',
                'schema' => [
                    'name' => [
                        'required' => true,
                        'types' => 'string',
                    ],
                    'email' => [
                        'types' => 'string',
                        'default' => 'default@example.com',
                    ],
                ],
            ],
        ],
        'expected' => [
            'user.name' => 'John',
            'user.email' => 'default@example.com',
        ],
    ],
    'complex_nested_validation' => [
        'data' => [
            'user' => [
                'profile' => [
                    'name' => 'John',
                ],
            ],
        ],
        'schema' => [
            'user' => [
                'types' => 'array',
                'schema' => [
                    'profile' => [
                        'types' => 'array',
                        'schema' => [
                            'name' => [
                                'required' => true,
                                'types' => 'string',
                            ],
                            'age' => [
                                'types' => 'int',
                                'default' => 18,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'expected' => [
            'user.profile.age' => 18,
        ],
    ],
    'combined_features_validation' => [
        'data' => [
            'order' => [
                'items' => [
                    'price' => '99.99',
                    'status' => 'active',
                ],
                'customer' => [
                    'name' => 'John',
                ],
            ],
        ],
        'schema' => [
            'order' => [
                'types' => 'array',
                'schema' => [
                    'items' => [
                        'types' => 'array',
                        'schema' => [
                            'price' => [
                                'required' => true,
                                'types' => ['float', 'string'],
                                'normalizer' => 'float',
                            ],
                            'status' => [
                                'required' => true,
                                'types' => 'string',
                                'choices' => ['active', 'inactive'],
                            ],
                        ],
                    ],
                    'customer' => [
                        'types' => 'array',
                        'schema' => [
                            'name' => [
                                'required' => true,
                                'types' => 'string',
                            ],
                            'email' => [
                                'types' => 'string',
                                'default' => 'no@email.com',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'expected' => [
            'order.items.price' => 99.99,
            'order.items.status' => 'active',
            'order.customer.name' => 'John',
            'order.customer.email' => 'no@email.com',
        ],
    ],
];
