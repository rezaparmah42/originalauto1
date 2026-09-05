# Patch 7.4 — Notification & Communication System

Date: 2026-08-07

Summary
-------
Added a notification and communication system for customers and workshop staff supporting notifications, SMS logging, chat, and repair updates.

Files added/updated
-------------------
- `database/notification_system_migration.sql` — creates `notifications`, `sms_logs`, `chat_messages`, `repair_updates`.
- `app/Models/Notification.php` — create/get/mark read/unread count.
- `app/Models/SMS.php` — SMS log and history.
- `app/Models/ChatMessage.php` — send/get conversation.
- `app/Models/RepairUpdate.php` — add update/get timeline.
- `app/Services/NotificationService.php` — notifyUser, notifyRepairStatus, notifyBooking, notifyPayment.
- `app/Services/SMSService.php` — provider abstraction for SMS sending.
- `app/Controllers/NotificationController.php` — customer notification center.
- `app/Controllers/ChatController.php` — repair chat endpoints.
- `app/Controllers/AdminCommunicationController.php` — admin communication views.
- `app/Views/notifications/index.php` — customer notifications view.
- `app/Views/chat/conversation.php` — repair chat UI.

Security
--------
- CSRF included where needed; prepared statements used in models; views use `e()` to escape output. Admin routes check admin role.

Validation
----------
Ran `php -l` on new core files — all returned no syntax errors.

Smoke tests
-----------
- `/notifications` — CLI smoke run rendered without fatal errors.
- `/repair/chat/1` — CLI smoke run rendered without fatal errors.
- `/admin/notifications` — CLI smoke run rendered without fatal errors.

Notes / Next steps
------------------
- Apply SQL migration to create tables before using features.
- Consider implementing real SMS provider and push notifications.
- Add technician->user linkage and stronger chat auth checks.

*** End Patch
