<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VWEB.DEV EAMS SaaS Plans & Features Matrix
    |--------------------------------------------------------------------------
    | This mapping strictly follows the official Client-Ready PDF Matrix.
    */

    'plans' => [
        
        // 1. STARTER PLAN
        // Focus: Basic tracking and task visibility
        'starter' => [
            'admin_dashboard',
            'employee_accounts',
            'department_management',
            'employee_management',
            'project_task_management',
            'check_in_check_out',
            'active_idle_tracking',
            'basic_daily_reports'
        ],

        // 2. BASIC PLAN
        // Focus: Screenshots and basic monitoring
        'basic' => [
            'admin_dashboard', 'employee_accounts', 'department_management', 'employee_management',
            'project_task_management', 'check_in_check_out', 'active_idle_tracking', 'basic_daily_reports',
            // Basic Addons:
            'screenshot_monitoring',
            'screenshot_gallery',
            'productivity_calculation',
            'website_app_tracking',
            'task_blockers'
        ],

        // 3. PRO PLAN
        // Focus: Communication, files, task control, and better reporting
        'pro' => [
            'admin_dashboard', 'employee_accounts', 'department_management', 'employee_management',
            'project_task_management', 'check_in_check_out', 'active_idle_tracking', 'basic_daily_reports',
            'screenshot_monitoring', 'screenshot_gallery', 'productivity_calculation', 'website_app_tracking', 'task_blockers',
            // Pro Addons:
            'manual_screenshot_trigger',
            'task_forwarding',
            'team_chat', // Full access
            'direct_messaging', // Full access
            'file_sharing',
            'voice_notes',
            'employee_rating_feedback',
            'custom_allowed_restricted_lists'
        ],

        // 4. ADVANCED PLAN
        // Focus: OCR, AI-tool tracking, alerts, and audit visibility
        'advanced' => [
            'admin_dashboard', 'employee_accounts', 'department_management', 'employee_management',
            'project_task_management', 'check_in_check_out', 'active_idle_tracking', 'basic_daily_reports',
            'screenshot_monitoring', 'screenshot_gallery', 'productivity_calculation', 'website_app_tracking', 'task_blockers',
            'manual_screenshot_trigger', 'task_forwarding', 'team_chat', 'direct_messaging', 'file_sharing', 'voice_notes',
            'employee_rating_feedback', 'custom_allowed_restricted_lists',
            // Advanced Addons:
            'ocr_screenshot_analysis',
            'ai_tool_tracking',
            'ai_detection_suite', // ChatGPT, Gemini, Claude, etc.
            'realtime_admin_alerts',
            'audio_alert_notifications',
            'approved_coding_environment',
            'audit_logs'
        ],

        // 5. PRIVATE-LABEL
        // Focus: Client-branded internal platform with Advanced features
        'private_label' => [
            'admin_dashboard', 'employee_accounts', 'department_management', 'employee_management',
            'project_task_management', 'check_in_check_out', 'active_idle_tracking', 'basic_daily_reports',
            'screenshot_monitoring', 'screenshot_gallery', 'productivity_calculation', 'website_app_tracking', 'task_blockers',
            'manual_screenshot_trigger', 'task_forwarding', 'team_chat', 'direct_messaging', 'file_sharing', 'voice_notes',
            'employee_rating_feedback', 'custom_allowed_restricted_lists',
            'ocr_screenshot_analysis', 'ai_tool_tracking', 'ai_detection_suite', 'realtime_admin_alerts',
            'audio_alert_notifications', 'approved_coding_environment', 'audit_logs',
            // Private Label Addons:
            'client_logo_branding',
            'custom_domain',
            'branded_reports_emails'
        ],

        // 6. ENTERPRISE
        // Focus: Large-scale requirements, custom rules, integrations, and SLA
        'enterprise' => [
            'admin_dashboard', 'employee_accounts', 'department_management', 'employee_management',
            'project_task_management', 'check_in_check_out', 'active_idle_tracking', 'basic_daily_reports',
            'screenshot_monitoring', 'screenshot_gallery', 'productivity_calculation', 'website_app_tracking', 'task_blockers',
            'manual_screenshot_trigger', 'task_forwarding', 'team_chat', 'direct_messaging', 'file_sharing', 'voice_notes',
            'employee_rating_feedback', 'custom_allowed_restricted_lists',
            'ocr_screenshot_analysis', 'ai_tool_tracking', 'ai_detection_suite', 'realtime_admin_alerts',
            'audio_alert_notifications', 'approved_coding_environment', 'audit_logs',
            'client_logo_branding', 'custom_domain', 'branded_reports_emails',
            // Enterprise Addons:
            'custom_integrations',
            'priority_sla'
        ]
    ]
];