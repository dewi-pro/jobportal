<?php

return [
    [
        'name' => 'Newsletters',
        'flag' => 'newsletter.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'newsletter.destroy',
        'parent_flag' => 'newsletter.index',
    ],
    [
        'name' => 'Newsletter Settings',
        'flag' => 'newsletter.settings',
        'parent_flag' => 'newsletter.index',
    ],
];
