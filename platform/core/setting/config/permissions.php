<?php

return [
    [
        'name' => 'Settings',
        'flag' => 'settings.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'General Settings',
        'flag' => 'settings.options',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Email',
        'flag' => 'settings.email',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Media',
        'flag' => 'settings.media',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Cronjob',
        'flag' => 'settings.cronjob',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Admin Appearance Settings',
        'flag' => 'settings.admin-appearance',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Cache Settings',
        'flag' => 'settings.cache',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Datatable Settings',
        'flag' => 'settings.datatables',
        'parent_flag' => 'settings.options',
    ],
    [
        'name' => 'Email Rules',
        'flag' => 'settings.email.rules',
        'parent_flag' => 'settings.options',
    ],
];
