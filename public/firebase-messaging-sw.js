// import { initializeApp } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js";
// import { getMessaging } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyBJE3YuOw1Jl5qDoC_sqyuiPnq3U0qcAdk",
    authDomain: "taftesh-74633.firebaseapp.com",
    projectId: "taftesh-74633",
    storageBucket: "taftesh-74633.appspot.com",
    messagingSenderId: "930391301074",
    appId: "1:930391301074:web:45a7ad03354d8d069dc60b",
    measurementId: "G-G2FVZL2SQ7"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(({ notification }) => {
    console.log("[firebase-messaging-sw.js] Received background message ");
    // Customize notification here
    const notificationTitle = notification.title;
    const notificationOptions = {
        body: notification.body,
    };

    if (notification.icon) {
        notificationOptions.icon = notification.icon;
    }

    self.registration.showNotification(notificationTitle, notificationOptions);
});
