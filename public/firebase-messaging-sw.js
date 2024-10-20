importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

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
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});
