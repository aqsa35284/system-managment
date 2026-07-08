<?php

/**
 * EAMS Official Feature Matrix — synced with Client-Ready Plans PDF (Minimum 5 User Model).
 * Core modules (dashboard, departments, employees, projects, tasks, attendance) are always on for every plan.
 */
return [

    /*
    | Premium features shown in Super Admin plan editor & admin sidebar locks.
    */
    'all' => [
        'screenshot_monitoring'     => 'Screenshot Monitoring',
        'screenshot_gallery'        => 'Screenshot Gallery',
        'productivity_calculation'  => 'Productivity Calculation',
        'website_app_tracking'      => 'Website/App Activity Tracking',
        'task_blockers'             => 'Task Blockers / Hurdles',
        'team_chat_limited'         => 'Limited Team Communication (Basic)',
        'manual_screenshot_trigger' => 'Manual Screenshot Trigger',
        'task_forwarding'           => 'Task Forwarding',
        'team_chat'                 => 'Team Chat / War Room',
        'file_sharing'              => 'File Sharing',
        'voice_notes'               => 'Voice Notes',
        'employee_rating_feedback'  => 'Employee Rating / Feedback',
        'custom_lists'              => 'Custom Allowed / Restricted Lists',
        'advanced_reporting'        => 'Advanced Shift Reports & Analytics',
        'ocr_screenshot_analysis'   => 'OCR Screenshot Analysis',
        'ai_tool_tracking'          => 'AI Tool Tracking (ChatGPT, Gemini, etc.)',
        'real_time_alerts'          => 'Real-time Admin Alerts',
        'audio_alerts'              => 'Audio Alert Notifications',
        'coding_env_detection'      => 'Approved Coding Environment Detection',
        'audit_logs'                => 'Audit Logs',
        'custom_branding'           => 'Client Logo & Branding',
        'custom_domain'             => 'Custom Domain / Subdomain',
        'branded_reports'           => 'Branded Reports & Emails',
        'custom_integrations'       => 'Custom Integrations (Enterprise)',
        'priority_sla'              => 'SLA / Priority Support (Enterprise)',
    ],

    /*
    | Legacy slug aliases — keeps older DB records working after renames.
    */
    'aliases' => [
        'manual_screenshots'       => 'manual_screenshot_trigger',
        'activity_tracking'        => 'website_app_tracking',
        'employee_rating'          => 'employee_rating_feedback',
        'ocr_analysis'             => 'ocr_screenshot_analysis',
        'screenshot_storage'       => 'screenshot_monitoring',
        'realtime_admin_alerts'    => 'real_time_alerts',
        'client_logo_branding'     => 'custom_branding',
        'branded_reports_emails'   => 'branded_reports',
        'ai_detection_suite'       => 'ai_tool_tracking',
    ],

    /*
    | Official PDF tier bundles (cumulative premium features per plan slug).
    */
    'tiers' => [
        'starter' => [],

        'basic' => [
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers',
            'team_chat_limited',
        ],

        'pro' => [
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers',
            'manual_screenshot_trigger',
            'task_forwarding',
            'team_chat',
            'file_sharing',
            'voice_notes',
            'employee_rating_feedback',
            'custom_lists',
            'advanced_reporting',
        ],

        'advanced' => [
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers',
            'manual_screenshot_trigger',
            'task_forwarding',
            'team_chat',
            'file_sharing',
            'voice_notes',
            'employee_rating_feedback',
            'custom_lists',
            'advanced_reporting',
            'ocr_screenshot_analysis',
            'ai_tool_tracking',
            'real_time_alerts',
            'audio_alerts',
            'coding_env_detection',
            'audit_logs',
        ],

        'private_label' => [
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers',
            'manual_screenshot_trigger',
            'task_forwarding',
            'team_chat',
            'file_sharing',
            'voice_notes',
            'employee_rating_feedback',
            'custom_lists',
            'advanced_reporting',
            'ocr_screenshot_analysis',
            'ai_tool_tracking',
            'real_time_alerts',
            'audio_alerts',
            'coding_env_detection',
            'audit_logs',
            'custom_branding',
            'custom_domain',
            'branded_reports',
        ],

        'enterprise' => [
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers',
            'manual_screenshot_trigger',
            'task_forwarding',
            'team_chat',
            'file_sharing',
            'voice_notes',
            'employee_rating_feedback',
            'custom_lists',
            'advanced_reporting',
            'ocr_screenshot_analysis',
            'ai_tool_tracking',
            'real_time_alerts',
            'audio_alerts',
            'coding_env_detection',
            'audit_logs',
            'custom_branding',
            'custom_domain',
            'branded_reports',
            'custom_integrations',
            'priority_sla',
        ],
    ],

    /*
    | Plan pricing defaults from PDF (used when seeding subscription_plans).
    */
    'pricing' => [
        'starter'       => ['name' => 'Starter',       'price' => 9999,   'extra_user_rate' => 1000, 'retention_days' => 0],
        'basic'         => ['name' => 'Basic',         'price' => 19999,  'extra_user_rate' => 1500, 'retention_days' => 7],
        'pro'           => ['name' => 'Pro',           'price' => 34999,  'extra_user_rate' => 2000, 'retention_days' => 15],
        'advanced'      => ['name' => 'Advanced',      'price' => 59999,  'extra_user_rate' => 3000, 'retention_days' => 30],
        'private_label' => ['name' => 'Private-Label', 'price' => 99999,  'extra_user_rate' => 3500, 'retention_days' => 30],
        'enterprise'    => ['name' => 'Enterprise',    'price' => 250000, 'extra_user_rate' => 5000, 'retention_days' => 90],
    ],
];
