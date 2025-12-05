<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification Test</title>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.0.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
</head>
<body>
    <h1>Real-Time Notification Test</h1>
    <button id="sendTest">Send Test Notification</button>

    <script>
        // Pass Laravel variables to JS
        window.Laravel = {
            userId: {{ 1 }},
            pusherKey: '{{ env('PUSHER_APP_KEY') }}',
            pusherCluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Initialize Echo
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: window.Laravel.pusherKey,
            cluster: window.Laravel.pusherCluster,
            forceTLS: true,
            encrypted: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                }
            }
        });

        // Listen on private channel
        window.Echo.private(`user.${window.Laravel.userId}`)
            .listen('NewNotificationEvent', (e) => {
                console.log('🚀 New Notification Received:', e.notification);
                alert('New notification: ' + e.notification.message);
            });

        // Send test notification via route
        document.getElementById('sendTest').addEventListener('click', () => {
            fetch('/test-notification')
                .then(res => res.text())
                .then(console.log)
                .catch(console.error);
        });
    </script>
</body>
</html>
