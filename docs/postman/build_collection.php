<?php
/**
 * Generates Postman Collection v2.1 JSON. Run: php docs/postman/build_collection.php
 */

function url(string $path): string
{
    return '{{base_url}}/api'.$path;
}

function accept(): array
{
    return [['key' => 'Accept', 'value' => 'application/json']];
}

function bearerAuth(): array
{
    return [
        'type' => 'bearer',
        'bearer' => [['key' => 'token', 'value' => '{{token}}', 'type' => 'string']],
    ];
}

function req(string $name, string $method, string $path, array $options = []): array
{
    $headers = accept();
    $body = null;

    if (! empty($options['json'])) {
        $headers[] = ['key' => 'Content-Type', 'value' => 'application/json'];
        $body = [
            'mode' => 'raw',
            'raw' => json_encode($options['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'options' => ['raw' => ['language' => 'json']],
        ];
    } elseif (! empty($options['urlencoded'])) {
        $ue = [];
        foreach ($options['urlencoded'] as $row) {
            $ue[] = [
                'key' => $row[0],
                'value' => $row[1],
                'type' => 'text',
                'description' => $row[2] ?? '',
            ];
        }
        $body = ['mode' => 'urlencoded', 'urlencoded' => $ue];
    } elseif (! empty($options['formdata'])) {
        $body = ['mode' => 'formdata', 'formdata' => $options['formdata']];
    }

    $request = [
        'method' => $method,
        'header' => array_merge($headers, $options['headers'] ?? []),
        'url' => url($path),
    ];
    if ($body !== null) {
        $request['body'] = $body;
    }
    if (! empty($options['bearer'])) {
        $request['auth'] = bearerAuth();
    }
    if (! empty($options['description'])) {
        $request['description'] = $options['description'];
    }

    return ['name' => $name, 'request' => $request];
}

function folder(string $name, array $items, ?array $folderAuth = null): array
{
    $f = ['name' => $name, 'item' => $items];
    if ($folderAuth !== null) {
        $f['auth'] = $folderAuth;
    }

    return $f;
}

$collection = [
    'info' => [
        '_postman_id' => 'c9d0e1f2-a3b4-5678-cdef-901234567890',
        'name' => 'Ibraheem Altamim API',
        'description' => implode("\n", [
            'Laravel API collection.',
            '',
            '**Variables** (Collection → Variables):',
            '- `base_url` = http://ibraheemaltamim.test',
            '- `token` = JWT from Login or Verify email (Bearer)',
            '',
            'Protected folders inherit Bearer {{token}}. Paste token after auth.',
        ]),
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        ['key' => 'base_url', 'value' => 'http://ibraheemaltamim.test'],
        ['key' => 'token', 'value' => ''],
        ['key' => 'course_id', 'value' => '1'],
        ['key' => 'opportunity_id', 'value' => '1'],
        ['key' => 'chat_id', 'value' => '1'],
        ['key' => 'research_idea_id', 'value' => '1'],
        ['key' => 'application_id', 'value' => '1'],
        ['key' => 'favourite_id', 'value' => '1'],
        ['key' => 'publication_id', 'value' => '1'],
        ['key' => 'service_id', 'value' => '1'],
        ['key' => 'notification_id', 'value' => ''],
        ['key' => 'dynamic_page_slug', 'value' => 'about'],
    ],
    'item' => [],
];

$collection['item'][] = folder('01 — Auth (Guest)', [
    req('Register', 'POST', '/register', [
        'urlencoded' => [
            ['name', 'Test User', 'required'],
            ['email', 'user@example.com', ''],
            ['phone', '', 'optional'],
            ['password', 'password', 'min 6'],
            ['password_confirmation', 'password', ''],
            ['role', 'researcher', 'mentor | researcher'],
        ],
        'description' => 'Creates user. Sends OTP to email.',
    ]),
    req('Verify email', 'POST', '/verify-email', [
        'urlencoded' => [
            ['email', 'user@example.com', ''],
            ['otp', '1234', '4 digits'],
        ],
        'description' => 'Returns JWT token on success.',
    ]),
    req('Resend OTP', 'POST', '/resend-otp', [
        'urlencoded' => [['email', 'user@example.com', '']],
    ]),
    req('Login', 'POST', '/login', [
        'urlencoded' => [
            ['email', 'user@example.com', ''],
            ['password', 'password', ''],
        ],
        'description' => 'Requires verified email.',
    ]),
    req('Forgot password', 'POST', '/forget-password', [
        'urlencoded' => [['email', 'user@example.com', '']],
    ]),
    req('Verify OTP (password reset)', 'POST', '/verify-otp', [
        'urlencoded' => [
            ['email', 'user@example.com', ''],
            ['otp', '1234', ''],
        ],
        'description' => 'Response includes `token` for reset-password.',
    ]),
    req('Reset password', 'POST', '/reset-password', [
        'urlencoded' => [
            ['email', 'user@example.com', ''],
            ['token', 'PASTE_FROM_VERIFY_OTP', ''],
            ['password', 'newsecret12', ''],
            ['password_confirmation', 'newsecret12', ''],
        ],
    ]),
    req('Social login', 'POST', '/social-login', [
        'urlencoded' => [
            ['token', 'OAUTH_ACCESS_TOKEN', 'required'],
            ['provider', 'google', 'google|facebook|apple|instagram'],
        ],
    ]),
]);

$collection['item'][] = folder('02 — Auth (JWT)', [
    req('Refresh token', 'GET', '/refresh-token', ['bearer' => true]),
    req('Logout', 'POST', '/logout', ['bearer' => true]),
    req('Me', 'GET', '/me', ['bearer' => true]),
    req('Update profile', 'POST', '/update-profile', [
        'bearer' => true,
        'formdata' => [
            ['key' => 'first_name', 'value' => 'John', 'type' => 'text'],
            ['key' => 'last_name', 'value' => 'Doe', 'type' => 'text'],
            ['key' => 'city', 'value' => '', 'type' => 'text'],
            ['key' => 'state', 'value' => '', 'type' => 'text'],
            ['key' => 'zip_code', 'value' => '', 'type' => 'text'],
            ['key' => 'gender', 'value' => 'male', 'type' => 'text', 'description' => 'male|female|others'],
            ['key' => 'phone', 'value' => '', 'type' => 'text'],
            ['key' => 'password', 'value' => '', 'type' => 'text', 'description' => 'optional'],
            ['key' => 'password_confirmation', 'value' => '', 'type' => 'text'],
            ['key' => 'avatar', 'description' => 'optional image', 'type' => 'file', 'src' => null],
            ['key' => 'cover_photo', 'description' => 'optional', 'type' => 'file', 'src' => null],
        ],
        'description' => 'multipart/form-data. Files optional.',
    ]),
    req('Change password', 'POST', '/update-password', [
        'bearer' => true,
        'urlencoded' => [
            ['current_password', 'old', 'required'],
            ['new_password', 'newpass12', ''],
            ['new_password_confirmation', 'newpass12', ''],
        ],
    ]),
    req('Clergy profile update', 'POST', '/clergy-profile-update', [
        'bearer' => true,
        'formdata' => [
            ['key' => 'first_name', 'value' => 'John', 'type' => 'text'],
            ['key' => 'last_name', 'value' => 'Doe', 'type' => 'text'],
            ['key' => 'phone', 'value' => '', 'type' => 'text'],
            ['key' => 'gender', 'value' => '', 'type' => 'text'],
            ['key' => 'religion', 'value' => '', 'type' => 'text'],
            ['key' => 'bio', 'value' => '', 'type' => 'text'],
            ['key' => 'avatar', 'type' => 'file', 'src' => null],
            ['key' => 'cover_photo', 'type' => 'file', 'src' => null],
        ],
        'description' => 'Only for users with role `clergy`.',
    ]),
], bearerAuth());

$collection['item'][] = folder('03 — App user (role user)', [
    req('Dashboard home', 'GET', '/dashboard-data/home', ['bearer' => true]),
    req('My notifications', 'GET', '/my-notifications', ['bearer' => true]),
    req('Mark notifications read', 'POST', '/my-notifications/mark-as-read', [
        'bearer' => true,
        'json' => ['notification_ids' => ['id-1', 'id-2']],
        'description' => 'Use actual notification id values from your DB.',
    ]),
    req('Delete notification', 'DELETE', '/my-notifications-delete/{{notification_id}}', ['bearer' => true]),
    req('Delete all notifications', 'DELETE', '/my-notifications-delete-all', ['bearer' => true]),
    req('My courses', 'GET', '/my-courses', ['bearer' => true]),
    req('Buy course', 'POST', '/courses/{{course_id}}/buy', [
        'bearer' => true,
        'urlencoded' => [
            ['payment_gateway', 'manual', 'optional'],
            ['payment_gateway_id', '', 'optional'],
            ['payment_method_id', '', 'optional'],
            ['pay_id', '', 'optional'],
        ],
    ]),
], bearerAuth());

$collection['item'][] = folder('04 — Mentor', [
    req('Research ideas list', 'GET', '/mentor/research-ideas?per_page=15', ['bearer' => true]),
    req('Create research idea', 'POST', '/mentor/research-ideas', [
        'bearer' => true,
        'urlencoded' => [
            ['study_title', 'Title', 'required'],
            ['study_descritions', 'Long description', 'API field spelling'],
            ['research_objectives', 'Objectives', 'required'],
            ['perposed_methodology', 'Methodology', 'API spelling perposed'],
        ],
        'description' => 'Can use form-urlencoded or raw JSON with same keys.',
    ]),
    req('Research idea show', 'GET', '/mentor/research-ideas/{{research_idea_id}}', ['bearer' => true]),
    req('Update research idea', 'PUT', '/mentor/research-ideas/{{research_idea_id}}', [
        'bearer' => true,
        'urlencoded' => [
            ['study_title', 'Title', ''],
            ['study_descritions', 'Desc', ''],
            ['research_objectives', 'Obj', ''],
            ['perposed_methodology', 'Meth', ''],
        ],
    ]),
    [
        'name' => 'Submit submission proof',
        'request' => [
            'auth' => bearerAuth(),
            'method' => 'POST',
            'header' => accept(),
            'body' => [
                'mode' => 'formdata',
                'formdata' => [
                    [
                        'key' => 'proof_files[]',
                        'type' => 'file',
                        'src' => null,
                        'description' => 'pdf,jpg,jpeg,png,webp; add multiple',
                    ],
                ],
            ],
            'url' => url('/mentor/research-ideas/{{research_idea_id}}/submission-proof'),
        ],
    ],
    req('Chats list', 'GET', '/mentor/chats', ['bearer' => true]),
    req('Chat show (+ messages)', 'GET', '/mentor/chats/{{chat_id}}', ['bearer' => true]),
    req('Send chat message', 'POST', '/mentor/chats/{{chat_id}}/messages', [
        'bearer' => true,
        'urlencoded' => [['message', 'Hello', 'required']],
    ]),
], bearerAuth());

$collection['item'][] = folder('05 — Researcher', [
    req('Applications list', 'GET', '/researcher/opportunity-applications?per_page=15', ['bearer' => true]),
    [
        'name' => 'Create opportunity application',
        'request' => [
            'auth' => bearerAuth(),
            'method' => 'POST',
            'header' => accept(),
            'body' => [
                'mode' => 'formdata',
                'formdata' => [
                    ['key' => 'opportunity_id', 'value' => '{{opportunity_id}}', 'type' => 'text'],
                    ['key' => 'first_name', 'value' => 'Jane', 'type' => 'text'],
                    ['key' => 'last_name', 'value' => 'Doe', 'type' => 'text'],
                    ['key' => 'email', 'value' => 'jane@example.com', 'type' => 'text'],
                    ['key' => 'phone', 'value' => '', 'type' => 'text'],
                    ['key' => 'highest_edu_level', 'value' => '', 'type' => 'text'],
                    ['key' => 'institution', 'value' => '', 'type' => 'text'],
                    ['key' => 'field_of_study', 'value' => '', 'type' => 'text'],
                    ['key' => 'research_experience', 'value' => '', 'type' => 'text'],
                    ['key' => 'current_position', 'value' => '', 'type' => 'text'],
                    ['key' => 'linkedin_profile', 'value' => '', 'type' => 'text'],
                    ['key' => 'personal_website', 'value' => '', 'type' => 'text'],
                    ['key' => 'cover_letter', 'value' => '', 'type' => 'text'],
                    ['key' => 'accept_term', 'value' => '1', 'type' => 'text', 'description' => 'required boolean'],
                    ['key' => 'documents[]', 'type' => 'file', 'src' => null, 'description' => 'optional multiple'],
                ],
            ],
            'url' => url('/researcher/opportunity-applications'),
        ],
    ],
    [
        'name' => 'Update application',
        'request' => [
            'auth' => bearerAuth(),
            'method' => 'PUT',
            'header' => accept(),
            'body' => [
                'mode' => 'formdata',
                'formdata' => [
                    ['key' => 'first_name', 'value' => 'Jane', 'type' => 'text'],
                    ['key' => 'email', 'value' => 'jane@example.com', 'type' => 'text'],
                ],
            ],
            'url' => url('/researcher/opportunity-applications/{{application_id}}'),
            'description' => 'Only while status is pending. Optional documents[].',
        ],
    ],
    req('Application show', 'GET', '/researcher/opportunity-applications/{{application_id}}', ['bearer' => true]),
    req('Application delete', 'DELETE', '/researcher/opportunity-applications/{{application_id}}', ['bearer' => true]),
    req('Favourites list', 'GET', '/researcher/favourites', ['bearer' => true]),
    req('Favourite add', 'POST', '/researcher/favourites', [
        'bearer' => true,
        'urlencoded' => [['opportunity_id', '{{opportunity_id}}', 'required']],
    ]),
    req('Favourite show', 'GET', '/researcher/favourites/{{favourite_id}}', ['bearer' => true]),
    req('Favourite remove', 'DELETE', '/researcher/favourites/{{favourite_id}}', ['bearer' => true]),
    req('Chats list', 'GET', '/researcher/chats', ['bearer' => true]),
    req('Chat show', 'GET', '/researcher/chats/{{chat_id}}', ['bearer' => true]),
    req('Send message', 'POST', '/researcher/chats/{{chat_id}}/messages', [
        'bearer' => true,
        'urlencoded' => [['message', 'Hi', '']],
    ]),
], bearerAuth());

$collection['item'][] = folder('06 — Public / Marketing', [
    req('Opportunities list', 'GET', '/opportunities?per_page=15&only_active=1', [
        'description' => 'Query: is_future=0|1, only_active (default true).',
    ]),
    req('Opportunity detail', 'GET', '/opportunities/{{opportunity_id}}', []),
    req('Courses list', 'GET', '/courses?per_page=10&search=&is_future=', []),
    req('Course detail', 'GET', '/courses/{{course_id}}', []),
    req('Publications list', 'GET', '/publications?per_page=10&search=&category_id=', []),
    req('Publication detail', 'GET', '/publications/{{publication_id}}', []),
    req('Services list', 'GET', '/services?per_page=10&search=&featured=', []),
    req('Service detail', 'GET', '/services/{{service_id}}', []),
    req('Partnership request (form)', 'POST', '/partnership-requests', [
        'urlencoded' => [
            ['full_name', 'Partner Name', 'required'],
            ['organization', '', 'optional'],
            ['email', 'partner@org.com', 'optional'],
            ['phone', '', 'optional'],
            ['message', 'We would like to collaborate.', 'optional'],
        ],
    ]),
    req('Calendly scheduling URL', 'GET', '/calendly/schedule-link', []),
    req('FAQs list', 'GET', '/faqs', []),
    req('FAQ categories', 'GET', '/faqs-categories', []),
    req('FAQ category show', 'GET', '/faqs-categories/1', []),
    req('Newsletter subscribe', 'POST', '/newsletter/subscribe', [
        'urlencoded' => [['email', 'sub@example.com', 'required']],
    ]),
    req('Social links', 'GET', '/social-links', []),
    req('Contact support', 'POST', '/contact-support-message/sent', [
        'urlencoded' => [
            ['full_name', 'User', 'required'],
            ['email', 'user@example.com', 'required'],
            ['message', 'Need help', 'required'],
        ],
    ]),
    req('System info', 'GET', '/system-info', []),
    req('Dynamic pages list', 'GET', '/dynamic-pages', []),
    req('Dynamic page by slug', 'GET', '/dynamic-pages/single/{{dynamic_page_slug}}', []),
]);

$collection['item'][] = folder('07 — CMS / Home page', [
    req('Hero section', 'GET', '/cms/home-page/hero-section', []),
    req('Counter section', 'GET', '/cms/home-page/counter-section', []),
    req('Why researchers choose', 'GET', '/cms/home-page/why-researchers-choose-section', []),
    req('Featured research opportunities', 'GET', '/cms/home-page/featured-research-opportunities-section', []),
    req('Meta academy section', 'GET', '/cms/home-page/meta-academy-section', []),
    req('Review section', 'GET', '/cms/home-page/review-section', []),
    req('Research journey section', 'GET', '/cms/home-page/research-journey-section', []),
    req('FAQ section', 'GET', '/cms/home-page/faq-section', []),
]);

$collection['item'][] = folder('08 — CMS / FAQ page', [
    req('FAQ hero (GET)', 'GET', '/cms/faq-page/hero-section', []),
    req('FAQ hero (POST)', 'POST', '/cms/faq-page/hero-section', [
        'urlencoded' => [
            ['title', '', 'optional'],
            ['sub_title', '', 'optional'],
            ['description', '', 'optional'],
        ],
    ]),
    req('FAQ questions section (GET)', 'GET', '/cms/faq-page/question-section', []),
    req('FAQ questions section (POST)', 'POST', '/cms/faq-page/question-section', [
        'urlencoded' => [
            ['title', '', 'optional'],
            ['description', '', 'optional'],
            ['button_text', '', 'optional'],
        ],
    ]),
]);

$collection['item'][] = folder('09 — CMS / Other pages', [
    req('Meta academy — hero', 'GET', '/cms/meta-academy-page/hero-section', []),
    req('Meta academy — counter', 'GET', '/cms/meta-academy-page/counter-section', []),
    req('Research page — section', 'GET', '/cms/research-page/research-section', []),
    req('Service page — section', 'GET', '/cms/service-page/service-section', []),
]);

$partnershipCms = [
    req('Hero', 'GET', '/cms/partnership-page/hero-section', []),
    req('Our mission', 'GET', '/cms/partnership-page/our-mission-section', []),
    req('About us', 'GET', '/cms/partnership-page/about-us-section', []),
    req('Partnership value', 'GET', '/cms/partnership-page/partnership-value-section', []),
    req('Core research', 'GET', '/cms/partnership-page/core-research-section', []),
    req('Systematic review', 'GET', '/cms/partnership-page/systematic-review-section', []),
    req('Manuscript', 'GET', '/cms/partnership-page/manuscript-section', []),
    req('Partnership model', 'GET', '/cms/partnership-page/partnership-model-section', []),
    req('Reviewer comment', 'GET', '/cms/partnership-page/reviewer-comment-section', []),
    req('Journal', 'GET', '/cms/partnership-page/journal-section', []),
    req('Research opportunities', 'GET', '/cms/partnership-page/research-opportunitie-section', []),
    req('Why partner', 'GET', '/cms/partnership-page/why-partner-section', []),
    req('Partner model', 'GET', '/cms/partnership-page/partner-model-section', []),
];

$collection['item'][] = folder('10 — CMS / Partnership page', $partnershipCms);

$outFile = __DIR__.DIRECTORY_SEPARATOR.'IbraheemAltamim-API.postman_collection.json';
file_put_contents($outFile, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

echo "Wrote {$outFile}\n";
