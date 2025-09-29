// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then(registration => {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, err => {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const micBtn = document.getElementById('mic-btn');

    sendBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    micBtn.addEventListener('click', startSpeechRecognition);

    function sendMessage() {
        const messageText = userInput.value.trim();
        if (messageText === '') return;
        appendMessage(messageText, 'user-message');
        userInput.value = '';
        processMessageWithAI(messageText);
    }

    async function processMessageWithAI(messageText) {
        if (!navigator.onLine) {
            appendMessage('คุณกำลังออฟไลน์ ระบบ AI ไม่พร้อมใช้งานในขณะนี้', 'bot-message');
            // Fallback to simple offline command processing could be implemented here.
            return;
        }

        try {
            const aiResponse = await fetch('/api.php?resource=ai', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: messageText })
            });

            if (!aiResponse.ok) throw new Error('AI service returned an error.');

            const aiData = await aiResponse.json();
            appendMessage(aiData.reply, 'bot-message');

            if (aiData.intent === 'create_appointment' && aiData.entities.title && aiData.entities.datetime) {
                const newAppointment = {
                    title: aiData.entities.title,
                    date: aiData.entities.datetime,
                    synced: false // Mark as not synced
                };
                addAppointment(newAppointment); // Save to local DB and trigger sync
            }
        } catch (error) {
            console.error("AI processing error:", error);
            appendMessage('ขออภัยครับ ระบบ AI ขัดข้อง โปรดลองใหม่อีกครั้ง', 'bot-message');
        }
    }

    function appendMessage(text, className) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', className);
        messageElement.textContent = text;
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function startSpeechRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            appendMessage('ขออภัยครับ เบราว์เซอร์ของคุณไม่รองรับฟังก์ชันเสียง', 'bot-message');
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'th-TH';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        micBtn.disabled = true;
        micBtn.textContent = 'กำลังฟัง...';
        recognition.start();

        recognition.onresult = (event) => {
            userInput.value = event.results[0][0].transcript;
            sendMessage();
        };
        recognition.onspeechend = () => recognition.stop();
        recognition.onend = () => {
            micBtn.disabled = false;
            micBtn.textContent = '🎙️';
        };
        recognition.onerror = (event) => appendMessage(`เกิดข้อผิดพลาด: ${event.error}`, 'bot-message');
    }

    // --- IndexedDB & Sync Functions ---
    let db;
    function initDB() {
        const request = indexedDB.open('appointments-db', 2); // Version 2 for schema update

        request.onerror = (event) => console.error('Database error:', event.target.errorCode);

        request.onsuccess = (event) => {
            db = event.target.result;
            console.log('Database opened successfully.');
            loadAppointments();
            syncAppointments(); // Attempt sync on load
        };

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            const objectStore = db.createObjectStore('appointments', { keyPath: 'id', autoIncrement: true });
            objectStore.createIndex('title', 'title', { unique: false });
            objectStore.createIndex('date', 'date', { unique: false });
            objectStore.createIndex('synced', 'synced', { unique: false });
            console.log('Database schema upgraded.');
        };
    }

    function addAppointment(appointment) {
        const transaction = db.transaction(['appointments'], 'readwrite');
        const objectStore = transaction.objectStore('appointments');
        const request = objectStore.add(appointment);

        request.onsuccess = (event) => {
            const savedAppointment = { ...appointment, id: event.target.result };
            console.log('Appointment saved locally:', savedAppointment);
            loadAppointments(); // Refresh UI
            syncAppointments(); // Attempt to sync with the server
            scheduleNotification(savedAppointment); // Schedule a notification
        };
        request.onerror = (event) => console.error('Error saving appointment locally:', event.target.errorCode);
    }

    function loadAppointments() {
        // Clear existing appointment messages to prevent duplicates
        chatBox.querySelectorAll('.appointment-item').forEach(el => el.remove());

        const transaction = db.transaction(['appointments'], 'readonly');
        const objectStore = transaction.objectStore('appointments');
        objectStore.getAll().onsuccess = (event) => {
            const appointments = event.target.result;
            if (appointments.length > 0) {
                const listHeader = appendMessage('นัดหมายที่บันทึกไว้:', 'bot-message');
                listHeader.classList.add('appointment-item');
                appointments.forEach(appt => {
                    const status = appt.synced ? '(ซิงค์แล้ว)' : '(รอซิงค์)';
                    const msgText = `${appt.title} - ${new Date(appt.date).toLocaleString('th-TH')} ${status}`;
                    const apptMsg = appendMessage(msgText, 'bot-message');
                    apptMsg.classList.add('appointment-item');
                });
            }
        };
    }

    async function syncAppointments() {
        if (!navigator.onLine) {
            console.log("Offline. Sync postponed.");
            return;
        }

        const transaction = db.transaction(['appointments'], 'readwrite');
        const objectStore = transaction.objectStore('appointments');
        const syncIndex = objectStore.index('synced');

        syncIndex.getAll(false).onsuccess = (event) => { // Get all items where 'synced' is false
            const unsyncedAppointments = event.target.result;
            if (unsyncedAppointments.length === 0) return;

            console.log(`Syncing ${unsyncedAppointments.length} appointments...`);
            unsyncedAppointments.forEach(async (appt) => {
                try {
                    const response = await fetch('/api.php?resource=appointments', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ title: appt.title, appointment_time: appt.date })
                    });
                    const data = await response.json();
                    if (response.ok && data.status === 'success') {
                        const updateTransaction = db.transaction(['appointments'], 'readwrite');
                        const updateStore = updateTransaction.objectStore('appointments');
                        appt.synced = true;
                        updateStore.put(appt);
                        console.log(`Appointment ${appt.id} synced.`);
                    } else {
                        console.warn(`Failed to sync appointment ${appt.id}:`, data.message);
                    }
                } catch (error) {
                    console.error(`Error syncing appointment ${appt.id}:`, error);
                }
            });
            // Refresh the UI after attempting to sync all
            setTimeout(loadAppointments, 1500);
        };
    }

    function requestNotificationPermission() {
        if ('Notification' in window) {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                } else {
                    console.log('Notification permission denied.');
                }
            });
        }
    }

    function scheduleNotification(appointment) {
        if ('Notification' in window && Notification.permission === 'granted') {
            // For demonstration, show notification 5 seconds after creation.
            // A real app would calculate the time until the appointment.
            console.log(`Scheduling notification for: ${appointment.title}`);
            setTimeout(() => {
                 new Notification('แจ้งเตือนนัดหมาย', {
                    body: `เรื่อง: ${appointment.title} - ${new Date(appointment.date).toLocaleString('th-TH')}`,
                    icon: 'images/icon-192x192.png'
                });
            }, 5000);
        }
    }

    // Initialize and add event listeners
    initDB();
    requestNotificationPermission();
    window.addEventListener('online', syncAppointments);
});