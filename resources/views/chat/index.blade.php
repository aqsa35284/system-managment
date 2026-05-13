@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.employee')

@section('content')

{{-- ⭐ Toast Container for Notifications --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<div class="max-w-[1600px] mx-auto h-[88vh] flex flex-col lg:flex-row gap-4 p-2 font-sans text-slate-200 bg-[#0B1120]">
    
    <div class="w-full lg:w-80 h-full bg-[#0F172A] rounded-[2rem] shadow-2xl flex flex-col overflow-hidden border border-blue-900/30">
        <div class="p-6 bg-gradient-to-br from-blue-600 to-indigo-700">
            <h3 class="text-white font-black uppercase tracking-widest flex items-center gap-2 text-xs">
                <i class="fas fa-bolt animate-pulse"></i> Communication Hub
            </h3>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-2">Broadcast Nodes</p>
            <div onclick="loadChat(0, 'Team War Room', 'main-office')" id="user-0" 
                 class="user-row flex items-center gap-3 p-4 rounded-2xl cursor-pointer hover:bg-white/5 transition-all border border-transparent">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 border border-blue-500/30 shadow-lg">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-black text-slate-200 uppercase">General Ops</p>
                </div>
            </div>

            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mt-6 mb-3 ml-2">Direct Uplink</p>
            @foreach($users as $user)
            <div onclick="loadChat({{ $user->id }}, '{{ $user->name }}', null)" id="user-{{ $user->id }}" 
                 class="user-row flex items-center gap-3 p-4 rounded-2xl cursor-pointer hover:bg-white/5 transition-all">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-white text-xs font-black border border-slate-700">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-black text-slate-300 uppercase">{{ $user->name }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex-1 h-full bg-[#0F172A]/50 rounded-[2.5rem] shadow-2xl border border-blue-900/20 flex flex-col overflow-hidden relative backdrop-blur-sm">
        
        <div id="chat-header" class="p-6 border-b border-white/5 bg-[#0F172A]/80 z-10">
            <div class="flex items-center gap-4">
                <div id="active-avatar" class="hidden w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black border border-blue-400/30"></div>
                <div>
                    <h2 id="active-user-name" class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Awaiting Signal...</h2>
                </div>
            </div>
        </div>

        <div id="chat-window" class="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:32px_32px]">
            </div>

        <div class="p-6 bg-[#0F172A] border-t border-white/5 relative">
            <form id="chat-form" onsubmit="event.preventDefault(); sendMessage();">
                
                <div id="file-preview-zone" class="hidden mb-4 p-4 bg-blue-600/10 border border-blue-500/30 rounded-2xl flex items-center justify-between">
                    <span id="file-name-display" class="text-xs font-bold text-blue-200"></span>
                    <button type="button" onclick="clearFile()" class="text-rose-500 hover:text-rose-400"><i class="fas fa-times-circle"></i></button>
                </div>

                <div class="flex items-center gap-3">
                    
                    <div id="default-input-area" class="flex flex-1 items-center gap-3 transition-all duration-300">
                        <label class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-400 cursor-pointer">
                            <input type="file" id="file-input" class="hidden" onchange="handleFileSelect(this)">
                            <i class="fas fa-paperclip text-xl"></i>
                        </label>
                        
                        <div class="flex-1">
                            <input type="hidden" id="form_receiver_id" value="">
                            <input type="hidden" id="form_group_name" value="">
                            <input type="text" id="message-input" autocomplete="off"
                                   class="w-full bg-slate-800/50 border border-slate-700 rounded-full px-6 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500" 
                                   placeholder="Type a message...">
                        </div>
                    </div>

                    <div id="recording-area" class="hidden flex-1 items-center justify-between bg-rose-500/10 border border-rose-500/30 rounded-full px-6 py-3 transition-all duration-300">
                        <div class="flex items-center gap-3 text-rose-500 font-bold">
                            <span class="w-3 h-3 bg-rose-500 rounded-full animate-pulse"></span>
                            <span id="recording-time">00:00</span>
                        </div>
                        <div class="flex gap-6 items-center">
                            <button type="button" onclick="cancelRecording()" class="text-slate-400 hover:text-rose-500" title="Delete Recording"><i class="fas fa-trash-alt text-xl"></i></button>
                            <button type="button" onclick="sendRecording()" class="text-emerald-400 hover:text-emerald-300" title="Send Recording"><i class="fas fa-paper-plane text-xl"></i></button>
                        </div>
                    </div>

                    <button type="button" id="mic-btn" onclick="startRecording()" class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg transition-all">
                        <i id="mic-icon" class="fas fa-microphone text-xl"></i>
                    </button>
                    <button type="submit" id="send-btn" class="hidden w-12 h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg transition-all">
                        <i class="fas fa-paper-plane text-xl"></i>
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let activeChatId = null;
    let activeChatGroup = null;
    let selectedFile = null;

    let mediaRecorder;
    let audioChunks = [];
    let recordInterval;
    let seconds = 0;

    const msgInput = document.getElementById('message-input');
    const defaultArea = document.getElementById('default-input-area');
    const recArea = document.getElementById('recording-area');
    const micBtn = document.getElementById('mic-btn');
    const sendBtn = document.getElementById('send-btn');
    const timeDisplay = document.getElementById('recording-time');

    msgInput.addEventListener('input', () => {
        if(msgInput.value.trim() !== '') {
            micBtn.classList.add('hidden');
            sendBtn.classList.remove('hidden');
        } else {
            micBtn.classList.remove('hidden');
            sendBtn.classList.add('hidden');
        }
    });

    async function startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            
            mediaRecorder.start();
            audioChunks = [];
            
            defaultArea.classList.add('hidden');
            recArea.classList.remove('hidden');
            micBtn.classList.add('hidden');
            
            seconds = 0;
            timeDisplay.innerText = "00:00";
            recordInterval = setInterval(() => {
                seconds++;
                const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
                const secs = String(seconds % 60).padStart(2, '0');
                timeDisplay.innerText = `${mins}:${secs}`;
            }, 1000);

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
        } catch (err) { alert("Microphone access denied!"); }
    }

    function cancelRecording() {
        if(mediaRecorder && mediaRecorder.state === "recording") mediaRecorder.stop();
        clearInterval(recordInterval);
        resetUI();
    }

    function sendRecording() {
        if(mediaRecorder && mediaRecorder.state === "recording") {
            mediaRecorder.onstop = () => {
                window.audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                sendMessage(); 
                resetUI();
            };
            mediaRecorder.stop();
            clearInterval(recordInterval);
        }
    }

    function resetUI() {
        defaultArea.classList.remove('hidden');
        recArea.classList.add('hidden');
        msgInput.value = '';
        micBtn.classList.remove('hidden');
        sendBtn.classList.add('hidden');
        window.audioBlob = null;
        clearFile();
    }

    function handleFileSelect(input) {
        if(input.files.length > 0) {
            selectedFile = input.files[0];
            document.getElementById('file-name-display').innerText = selectedFile.name;
            document.getElementById('file-preview-zone').classList.remove('hidden');
            micBtn.classList.add('hidden');
            sendBtn.classList.remove('hidden');
        }
    }

    function clearFile() {
        selectedFile = null;
        document.getElementById('file-input').value = '';
        document.getElementById('file-preview-zone').classList.add('hidden');
        if(msgInput.value.trim() === '') {
            micBtn.classList.remove('hidden');
            sendBtn.classList.add('hidden');
        }
    }

    function formatMessage(text) {
        if(!text) return '';
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, url => `<a href="${url}" target="_blank" class="text-blue-200 underline">${url}</a>`);
    }

    async function sendMessage() {
        const receiverId = document.getElementById('form_receiver_id').value;
        const groupName = document.getElementById('form_group_name').value;
        
        if(!msgInput.value.trim() && !selectedFile && !window.audioBlob) return;

        const formData = new FormData();
        formData.append('message', msgInput.value);
        if(receiverId) formData.append('receiver_id', receiverId);
        if(groupName) formData.append('group_name', groupName);
        if(selectedFile) formData.append('file', selectedFile);
        if(window.audioBlob) formData.append('voice', window.audioBlob, 'voice_note.webm');

        resetUI();

        try {
            const response = await fetch("{{ url('/chat/send') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            if((await response.json()).success) refreshChat(false);
        } catch (error) { console.error(error); }
    }

    async function deleteMsg(id, btn) {
        if(!confirm('Delete message for everyone?')) return;
        try {
            const res = await fetch(`/chat/message/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if((await res.json()).success) {
                btn.closest('.message-container').remove();
            }
        } catch(e) { console.error(e); }
    }

    function addMessageToWindow(msg) {
        if(document.getElementById(`msg-${msg.id}`)) return;

        const win = document.getElementById('chat-window');
        const isMe = msg.sender_id == {{ auth()->id() }};
        
        let contentHtml = msg.message ? `<p class="text-[13px] leading-relaxed">${formatMessage(msg.message)}</p>` : '';
        
        if(msg.voice_path) {
            contentHtml += `
                <div class="mt-2 flex items-center gap-2 bg-black/20 rounded-full pr-4">
                    <audio controls class="h-10 w-[200px] outline-none">
                        <source src="/storage/${msg.voice_path}" type="audio/webm">
                    </audio>
                    <a href="/chat/download/${msg.id}" title="Download Voice Note" class="text-slate-400 hover:text-white"><i class="fas fa-download"></i></a>
                </div>`;
        }

        if(msg.file_path) {
            const isImg = msg.file_name.match(/\.(jpg|jpeg|png|gif)$/i);
            if(isImg) {
                contentHtml += `
                    <div class="mt-2 relative group overflow-hidden rounded-xl">
                        <img src="/storage/${msg.file_path}" class="w-full max-w-[250px]">
                        <a href="/chat/download/${msg.id}" class="absolute bottom-2 right-2 bg-black/60 text-white w-8 h-8 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity"><i class="fas fa-download"></i></a>
                    </div>`;
            } else {
                contentHtml += `
                    <div class="mt-2 flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                        <div class="w-10 h-10 bg-blue-500/20 text-blue-400 flex items-center justify-center rounded-lg"><i class="fas fa-file-alt text-xl"></i></div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold truncate">${msg.file_name}</p>
                        </div>
                        <a href="/chat/download/${msg.id}" class="text-blue-400 hover:text-blue-300"><i class="fas fa-download text-lg"></i></a>
                    </div>`;
            }
        }

        const deleteBtn = isMe ? `<button onclick="deleteMsg(${msg.id}, this)" class="text-slate-400 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Delete"><i class="fas fa-trash-alt text-xs"></i></button>` : '';

        const messageHtml = `
            <div id="msg-${msg.id}" class="flex ${isMe ? 'justify-end' : 'justify-start'} mb-4 message-container group animate-in fade-in slide-in-from-bottom-2">
                ${isMe ? `<div class="mr-3 flex items-center">${deleteBtn}</div>` : ''}
                <div class="max-w-[70%]">
                    <div class="p-3 ${isMe ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm' : 'bg-slate-800 text-slate-200 rounded-2xl rounded-tl-sm'} shadow-md relative">
                        ${!isMe && activeChatGroup ? `<p class="text-[10px] font-black text-blue-400 mb-1">${msg.sender?.name || 'User'}</p>` : ''}
                        ${contentHtml}
                        <div class="text-right mt-1">
                            <span class="text-[9px] opacity-70">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>
                </div>
                ${!isMe ? `<div class="ml-3 flex items-center">${deleteBtn}</div>` : ''}
            </div>`;
        
        win.insertAdjacentHTML('beforeend', messageHtml);
        win.scrollTop = win.scrollHeight;
    }

    function loadChat(id, name, group) {
        activeChatId = id; activeChatGroup = group;
        document.getElementById('form_receiver_id').value = (id === 0) ? '' : id;
        document.getElementById('form_group_name').value = group || '';
        document.getElementById('active-user-name').innerText = name;
        document.querySelectorAll('.user-row').forEach(r => r.classList.remove('bg-white/10'));
        document.getElementById(`user-${id}`).classList.add('bg-white/10');
        refreshChat(true);
    }

    async function refreshChat(isFirstLoad = false) {
        if (!activeChatId && !activeChatGroup) return;
        try {
            const response = await fetch("{{ url('/chat/fetch') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ 
                    receiver_id: document.getElementById('form_receiver_id').value, 
                    group_name: document.getElementById('form_group_name').value 
                })
            });
            const data = await response.json();
            
            if(isFirstLoad) {
                document.getElementById('chat-window').innerHTML = '';
            }

            let hasNewMsg = false;
            data.forEach(msg => {
                if(!document.getElementById(`msg-${msg.id}`)) {
                    addMessageToWindow(msg);
                    hasNewMsg = true;
                }
            });

            const currentIds = data.map(m => `msg-${m.id}`);
            document.querySelectorAll('.message-container').forEach(el => {
                if(!currentIds.includes(el.id)) {
                    el.remove();
                }
            });

            if(isFirstLoad || hasNewMsg) {
                document.getElementById('chat-window').scrollTop = document.getElementById('chat-window').scrollHeight;
            }
            
        } catch (e) {}
    }

    // ⭐ NAYA FIX: Toast Notifications Generator
    function spawnToast(title, message, icon = 'fa-comment-dots') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        try {
            const audio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA'); 
            audio.volume = 0.5; audio.play().catch(()=>{});
        } catch(e){}

        const toast = document.createElement('div');
        toast.className = `glass-effect border-l-4 border-blue-500/50 bg-blue-600/20 text-blue-400 rounded-xl p-4 w-80 shadow-[0_10px_30px_rgba(0,0,0,0.5)] pointer-events-auto flex gap-4 items-start toast-enter`;
        
        toast.innerHTML = `
            <div class="mt-0.5 rounded-full p-2 bg-black/30 shadow-inner">
                <i class="fas ${icon} text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">${title}</h4>
                <p class="text-[11px] text-slate-300 font-medium leading-relaxed">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-500 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.replace('toast-enter', 'toast-leave');
            setTimeout(() => toast.remove(), 400);
        }, 6000);
    }

    // ⭐ NAYA FIX: Global Notification Poller (Page load hote hi aur phir har 5s baad)
    let lastMessageCount = 0;
    function checkSystemAlerts() {
        fetch("{{ url('/chat/unread-count') }}")
        .then(res => res.json())
        .then(data => {
            if (data.count > 0) {
                // Toast sirf tab show karein agar naya message aaya hai aur user uski chat me nahi hai
                if (data.count > lastMessageCount) {
                    spawnToast(
                        'Incoming Transmission', 
                        `You have a new secure message from <b class="text-white">${data.sender_name}</b>.`
                    );
                }
                lastMessageCount = data.count;
            } else {
                lastMessageCount = 0;
            }
        }).catch(e => {});
    }

    // Timing Setup
    setInterval(() => { if (activeChatId || activeChatGroup) refreshChat(false); }, 5000);

    // Chat refresh aur alert system ko thora alag time pe run kiya gaya hai taake load na pare
    setTimeout(() => {
        checkSystemAlerts();
        setInterval(checkSystemAlerts, 5000);
    }, 2000);
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
    .glass-effect { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    
    @keyframes toast-slide-in {
        0% { transform: translateX(100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    @keyframes toast-slide-out {
        0% { transform: translateX(0); opacity: 1; }
        100% { transform: translateX(100%); opacity: 0; }
    }
    .toast-enter { animation: toast-slide-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    .toast-leave { animation: toast-slide-out 0.4s ease-in forwards; }
</style>
@endsection