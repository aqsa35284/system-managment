import './bootstrap';

// 🛠️ Variables Initialization (Blade se global window object mein milenge)
const authId = window.UserId;
const userDeptId = window.UserDeptId;

if (authId) {
    // ⭐ 1. Listening for Personal Messages (Private Channel)
    Echo.private(`chat.user.${authId}`)
        .listen('MessageSent', (data) => {
            console.log("🛰️ Signal Received:", data);
            
            // Check karein ke kya wahi chat window khuli hai jisne message bheja?
            if(window.currentChatId == data.message.sender_id) {
                appendMessage(data.message, false); 
                
                // Sound logic with error handling
                try {
                    let audio = new Audio('/assets/sounds/notify.mp3');
                    audio.play();
                } catch(e) { console.log("Sound play failed"); }
            } else {
                // Background notification (Jaise WhatsApp ka red dot hota hai)
                showNotificationBadge(data.message.sender_id);
                console.log("New message from: " + data.sender_name);
            }
        });

    // ⭐ 2. Listening for Group/Department Messages (Presence Channel)
    if(userDeptId) {
        Echo.join(`chat.dept.${userDeptId}`)
            .listen('MessageSent', (data) => {
                // Apne bheje hue message ko dobara append na karein (duplicate prevent)
                if(data.message.sender_id != authId && window.currentDeptId == data.message.department_id) {
                    appendMessage(data.message, false);
                }
            });
    }
}

// Global UI helper to append messages
window.appendMessage = function(msg, isMine) {
    const win = document.getElementById('chat-window');
    if(!win) return;

    let content = msg.message;
    if(msg.message_type === 'voice') {
        content = `<audio controls class="h-8"><source src="/storage/${msg.file_path}" type="audio/webm"></audio>`;
    } else if(msg.message_type === 'file') {
        content = `<a href="/storage/${msg.file_path}" class="text-indigo-400 underline text-[10px]" target="_blank">📎 ATTACHMENT</a>`;
    }

    const html = `
        <div class="flex ${isMine ? 'justify-end' : 'justify-start'} animate-in fade-in slide-in-from-bottom-2">
            <div class="${isMine ? 'bg-indigo-600 text-white rounded-t-2xl rounded-l-2xl' : 'bg-white/10 text-slate-200 rounded-t-2xl rounded-r-2xl'} p-4 max-w-md shadow-xl border border-white/5">
                <p class="text-[11px] font-medium">${content}</p>
                <span class="text-[7px] opacity-50 block mt-2 text-right uppercase font-mono">${new Date().toLocaleTimeString()}</span>
            </div>
        </div>`;
    win.insertAdjacentHTML('beforeend', html);
    win.scrollTop = win.scrollHeight;
}