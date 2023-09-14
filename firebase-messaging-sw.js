importScripts("https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js");
importScripts("https://www.gstatic.com/firebasejs/7.16.1/firebase-analytics.js");
firebase.initializeApp({
  apiKey: "AIzaSyCuB53gY090zhFPT48Dd3AjcJ90WhfclHM",
  authDomain: "bytelover-android.firebaseapp.com",
  projectId: "bytelover-android",
  storageBucket: "bytelover-android.appspot.com",
  messagingSenderId: "228849322669",
  appId: "1:228849322669:web:076f1f15909d03824dd424",
  measurementId: "G-YBPDFVENSM"
});
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon,
    data: payload.data
  };
  return self.registration.showNotification(notificationTitle, notificationOptions);
});
self.addEventListener('notificationclick', function(event) {
  const d = event.notification.data;
  if (d && d.url) {
  event.waitUntil(
        clients.matchAll({type: 'window'}).then( windowClients => {
            // Check if there is already a window/tab open with the target URL
            for (var i = 0; i < windowClients.length; i++) {
                var client = windowClients[i];
                // If so, just focus it.
                if (client.url === d.url && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, then open the target URL in a new window/tab.
            if (clients.openWindow) {
                return clients.openWindow(d.url);
            }
        })
    )
  }
  event.notification.close();
});
self.addEventListener('activate', function(event) {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('install', function(event) {
  event.waitUntil(self.skipWaiting());
});