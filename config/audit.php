<?php

// São as tabelas que não serão auditaveis

return [
    'excluded_models' => [
        App\Models\Audit::class,
    ],
    'excluded_tables' => [
        'migrations',
    ],
    'use_queue' => false,
    'queue_name' => 'audits',
];