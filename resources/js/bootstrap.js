import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY, // Check karein ye line sahi hai
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});