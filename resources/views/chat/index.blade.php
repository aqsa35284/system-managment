@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.employee')

@section('content')
<div class="max-w-7xl mx-auto h-[85vh] flex flex-col lg:flex-row gap-6 p-4 lg:p-0">
    
    <!-- Sidebar with Chat List -->
    <div class="w-full lg:w-1/3 h-full bg-white rounded-2xl shadow-xl border border-emerald-100 flex flex-col overflow-hidden">
        <div class="p-6 border-b border-emerald-100 bg-gradient-to-r from-emerald-600 to-teal-600">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-comments"></i> Communication Hub
                </h3>
                <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full font-medium">
                    {{ $users->count() + 1 }} Active
                </span>
            </div>
            <div class="relative">
                <input type="text" placeholder="Search conversations..." 
                       class="w-full bg-white/20 backdrop-blur-sm text-white placeholder-white/70 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/30">
                <i class="fas fa-search absolute right-3 top-2.5 text-white/70 text-sm"></i>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <!-- Team War Room -->
            <div onclick="loadChat(0, 'Team War Room', 'main-office')" id="user-0" 
                 class="user-row flex items-center gap-3 p-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all group">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">Team War Room</p>
                        <span class="text-xs text-gray-500">2:45 PM</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate">John: Let's discuss the project...</p>
                </div>
                <div class="bg-emerald-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                    3
                </div>
            </div>

            <div class="px-3 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Direct Messages</p>
            </div>

            @foreach($users as $user)
            <div onclick="loadChat({{ $user->id }}, '{{ $user->name }}', null)" id="user-{{ $user->id }}" 
                 class="user-row flex items-center gap-3 p-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all group">
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-shadow">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 {{ rand(0, 1) ? 'bg-green-500' : 'bg-gray-400' }} border-2 border-white rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <span class="text-xs text-gray-500">{{ rand(1, 12) }}:{{ rand(10, 59) }} {{ rand(0, 1) ? 'AM' : 'PM' }}</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ rand(0, 1) ? 'You: ' : '' }}{{ rand(0, 1) ? 'Sure, let me check that' : 'Thanks for the update!' }}</p>
                </div>
                @if(rand(0, 3) === 1)
                <div class="bg-emerald-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                    {{ rand(1, 9) }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Chat Window -->
    <div class="flex-1 h-full bg-white rounded-2xl shadow-xl border border-emerald-100 flex flex-col overflow-hidden">
        <!-- Chat Header -->
        <div id="chat-header" class="p-4 border-b border-emerald-100 bg-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="active-avatar" class="hidden w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold shadow-md"></div>
                    <div>
                        <h2 id="active-user-name" class="text-base font-semibold text-gray-400">Select a conversation</h2>
                        <p id="connection-status" class="hidden text-xs text-green-500 font-medium flex items-center gap-1 mt-0.5">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> 
                            Active now
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
                        <i class="fas fa-phone text-sm"></i>
                    </button>
                    <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
                        <i class="fas fa-video text-sm"></i>
                    </button>
                    <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div id="chat-window" class="flex-1 overflow-y-auto p-4 bg-gray-50 custom-scrollbar">
            <div class="h-full flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-comments text-3xl text-gray-400"></i>
                </div>
                <p class="text-lg font-semibold text-gray-700 mb-1">Welcome to Chat</p>
                <p class="text-sm text-gray-500">Select a conversation to start messaging</p>
            </div>
        </div>

        <!-- Message Input Area -->
        <div class="p-4 bg-white border-t border-emerald-100">
            <div id="typing-indicator" class="hidden text-xs text-gray-500 mb-2 ml-2">
                <span class="inline-flex">
                    <span class="animate-pulse">Someone is typing</span>
                    <span class="flex ml-1">
                        <span class="animate-bounce">.</span>
                        <span class="animate-bounce" style="animation-delay: 0.1s">.</span>
                        <span class="animate-bounce" style="animation-delay: 0.2s">.</span>
                    </span>
                </span>
            </div>
            
            <div class="flex items-end gap-2">
                <button class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fas fa-paperclip text-sm"></i>
                </button>
                
                <div class="flex-1 relative">
                    <input type="hidden" id="form_receiver_id" value="">
                    <input type="hidden" id="form_group_name" value="">
                    
                    <textarea id="message-input" 
                              class="w-full bg-gray-100 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-500 max-h-32" 
                              placeholder="Type a message..."
                              rows="1"
                              onkeypress="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }"></textarea>
                </div>
                
                <button class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fas fa-smile text-sm"></i>
                </button>
                
                <button onclick="sendMessage()" class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl flex items-center justify-center shadow-md transition-all transform hover:scale-105 active:scale-95">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.UserId = {{ auth()->id() }};
    let activeChatId = null;
    let activeChatGroup = null;
    
    function loadChat(id, name, group) {
        // Update active chat variables
        activeChatId = id;
        activeChatGroup = group;
        
        // Update form inputs
        document.getElementById('form_receiver_id').value = (id === 0) ? '' : id;
        document.getElementById('form_group_name').value = group || '';
        
        // Update UI elements
        document.getElementById('active-user-name').innerText = name;
        document.getElementById('active-user-name').classList.replace('text-gray-400', 'text-gray-900');
        
        if (id === 0) {
            // War Room
            document.getElementById('active-avatar').innerHTML = '<i class="fas fa-users"></i>';
            document.getElementById('active-avatar').className = 'w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md';
        } else {
            // Direct message
            document.getElementById('active-avatar').innerText = name.charAt(0).toUpperCase();
            document.getElementById('active-avatar').className = 'w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md';
        }
        
        document.getElementById('active-avatar').classList.remove('hidden');
        document.getElementById('connection-status').classList.remove('hidden');
        
        // Highlight active user in sidebar
        document.querySelectorAll('.user-row').forEach(row => {
            row.classList.remove('bg-emerald-50', 'border-l-4', 'border-emerald-500');
        });
        document.getElementById(`user-${id}`).classList.add('bg-emerald-50', 'border-l-4', 'border-emerald-500');
        
        refreshChat(true);
    }

    async function sendMessage() {
        const input = document.getElementById('message-input');
        const receiverId = document.getElementById('form_receiver_id').value;
        const groupName = document.getElementById('form_group_name').value;
        
        if(!input.value.trim()) return;

        // Show message immediately for better UX
        const tempMsg = {
            sender_id: window.UserId,
            message: input.value,
            created_at: new Date().toISOString(),
            temp: true
        };
        
        // Add message to UI immediately
        addMessageToWindow(tempMsg);
        
        try {
            const response = await fetch("{{ url('/chat/send') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    group_name: groupName,
                    message: input.value
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to send message');
            }
            
            // Refresh chat to get the real message with ID
            refreshChat(false);
        } catch (error) {
            console.error('Error sending message:', error);
            // Show error state in UI
        }
        
        input.value = '';
        // Reset textarea height
        input.style.height = 'auto';
    }

    function addMessageToWindow(msg) {
        const win = document.getElementById('chat-window');
        const isMe = msg.sender_id == window.UserId;
        const isGroup = activeChatGroup !== '';
        
        // Remove empty state if this is the first message
        if (win.querySelector('.flex-col.items-center')) {
            win.innerHTML = '';
        }
        
        const messageHtml = `
            <div class="flex ${isMe ? 'justify-end' : 'justify-start'} mb-4 message-container">
                <div class="max-w-[75%] ${isMe ? 'order-2' : 'order-1'}">
                    ${isGroup && !isMe ? `
                        <p class="text-xs font-medium text-gray-600 mb-1 ml-2">
                            ${msg.sender ? msg.sender.name : 'Unknown'}
                        </p>
                    ` : ''}
                    
                    <div class="p-3 rounded-2xl ${isMe ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-br-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-sm shadow-sm'} ${msg.temp ? 'opacity-70' : ''}">
                        <p class="text-sm">${msg.message}</p>
                        <div class="flex items-center justify-end mt-1 gap-1">
                            <p class="text-xs ${isMe ? 'text-white/70' : 'text-gray-500'}">
                                ${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                            </p>
                            ${isMe ? `<i class="fas fa-check${msg.temp ? '' : '-double'} text-xs ${msg.temp ? 'text-white/50' : 'text-white/70'}"></i>` : ''}
                        </div>
                    </div>
                </div>
            </div>`;
        
        win.insertAdjacentHTML('beforeend', messageHtml);
        win.scrollTop = win.scrollHeight;
    }

    async function refreshChat(isFirstLoad = false) {
        if (!activeChatId && !activeChatGroup) return;
        
        const receiverId = document.getElementById('form_receiver_id').value;
        const groupName = document.getElementById('form_group_name').value;
        
        try {
            const response = await fetch("{{ url('/chat/fetch') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ receiver_id: receiverId, group_name: groupName })
            });

            const data = await response.json();
            const win = document.getElementById('chat-window');
            
            if (data.length === 0) {
                // Show empty state
                win.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-700 mb-1">No messages yet</p>
                        <p class="text-sm text-gray-500">Start the conversation with a message</p>
                    </div>`;
                return;
            }
            
            win.innerHTML = '';
            data.forEach(msg => addMessageToWindow(msg));
            
            if(isFirstLoad) win.scrollTop = win.scrollHeight;
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    // Auto-refresh chat every 3 seconds
    setInterval(() => {
        if (activeChatId || activeChatGroup) {
            refreshChat(false);
        }
    }, 3000);
    
    // Auto-resize textarea
    document.getElementById('message-input').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 128) + 'px';
    });
    
    // Simulate typing indicator
    let typingTimer;
    document.getElementById('message-input').addEventListener('input', function() {
        if (this.value.trim()) {
            document.getElementById('typing-indicator').classList.remove('hidden');
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                document.getElementById('typing-indicator').classList.add('hidden');
            }, 1000);
        } else {
            document.getElementById('typing-indicator').classList.add('hidden');
        }
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection