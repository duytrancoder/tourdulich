<?php if (!empty($_SESSION['login'])): ?>
<!-- Ensure Tailwind CSS is loaded, but avoid collision if already present -->
<script src="https://cdn.tailwindcss.com"></script>

<div id="chat-widget" class="fixed bottom-6 right-6 z-50 font-sans">
    
    <!-- Chat Window (hidden by default) -->
    <div id="chat-window" class="hidden flex flex-col bg-white w-80 h-96 rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transition-all duration-300 transform scale-95 origin-bottom-right opacity-0 absolute bottom-[72px] right-0">
        <!-- Header -->
        <div class="bg-teal-700 text-white p-4 flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">Hỗ trợ trực tuyến</h3>
                    <p class="text-xs text-teal-100">Chúng tôi sẵn sàng giúp đỡ</p>
                </div>
            </div>
            <button id="close-chat" class="text-teal-100 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3">
            <div class="text-center text-xs text-gray-400 my-2">Bắt đầu cuộc trò chuyện</div>
            <!-- Messages will be appended here via JS -->
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-gray-100">
            <form id="chat-form" class="flex gap-2 relative">
                <input type="text" id="chat-input" class="flex-1 bg-gray-100 border-transparent rounded-full px-4 py-2 text-sm focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none transition-all" placeholder="Nhập tin nhắn..." autocomplete="off" required>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white rounded-full w-9 h-9 flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Chat Bubble Toggle -->
    <button id="chat-toggle" class="absolute bottom-0 right-0 bg-teal-600 hover:bg-teal-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg shadow-teal-600/30 transition-transform transform hover:scale-105 active:scale-95 focus:outline-none z-10">
        <!-- Unread Badge -->
        <span id="unread-badge" class="hidden absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">0</span>
        <!-- Chat Icon -->
        <svg id="chat-icon-open" class="w-6 h-6 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <!-- Close Icon -->
        <svg id="chat-icon-close" class="w-6 h-6 absolute transition-opacity duration-300 opacity-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const closeChat = document.getElementById('close-chat');
    const chatWindow = document.getElementById('chat-window');
    const iconOpen = document.getElementById('chat-icon-open');
    const iconClose = document.getElementById('chat-icon-close');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const unreadBadge = document.getElementById('unread-badge');
    
    let isChatOpen = false;
    let pollInterval = null;
    let lastMessageId = 0;

    // Toggle logic
    function toggleChat() {
        isChatOpen = !isChatOpen;
        if (isChatOpen) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('scale-95', 'opacity-0');
                chatWindow.classList.add('scale-100', 'opacity-100');
            }, 10);
            iconOpen.classList.add('opacity-0');
            iconClose.classList.remove('opacity-0');
            unreadBadge.classList.add('hidden');
            scrollToBottom();
            fetchMessages(); // fetch immediately
            // Start polling
            pollInterval = setInterval(fetchMessages, 3000);
        } else {
            chatWindow.classList.remove('scale-100', 'opacity-100');
            chatWindow.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                chatWindow.classList.add('hidden');
            }, 300);
            iconOpen.classList.remove('opacity-0');
            iconClose.classList.add('opacity-0');
            // Stop polling
            clearInterval(pollInterval);
            // Re-start unread polling
            pollUnread();
        }
    }

    chatToggle.addEventListener('click', toggleChat);
    closeChat.addEventListener('click', toggleChat);

    // Intercept navbar links for "Yêu cầu hỗ trợ" to open the chat widget
    document.querySelectorAll('a[href$="issue"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isChatOpen) {
                toggleChat();
            } else {
                chatWindow.classList.add('scale-105');
                setTimeout(() => chatWindow.classList.remove('scale-105'), 200);
            }
        });
    });

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function appendMessage(msg) {
        const isUser = msg.Sender === 'user';
        const alignClass = isUser ? 'self-end flex-row-reverse' : 'self-start';
        const bgClass = isUser ? 'bg-teal-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 shadow-sm rounded-bl-none';
        
        const html = `
            <div class="flex flex-col ${alignClass} max-w-[85%] animate-fade-in-up">
                <div class="${bgClass} px-3 py-2 rounded-2xl text-[14px] leading-relaxed break-words">
                    ${msg.Message}
                </div>
                <div class="text-[10px] text-gray-400 mt-1 px-1 ${isUser ? 'text-right' : 'text-left'}">
                    ${formatTime(msg.CreatedAt)}
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', html);
        lastMessageId = Math.max(lastMessageId, msg.id);
    }

    function fetchMessages() {
        fetch('<?php echo BASE_URL; ?>chat/getMessages')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    // Simple logic: if new messages, re-render all (or just append)
                    // For safety, let's clear and re-render to handle read states easily, 
                    // OR only append if id > lastMessageId.
                    
                    const newMessages = res.data.filter(m => m.id > lastMessageId);
                    if (newMessages.length > 0) {
                        newMessages.forEach(appendMessage);
                        scrollToBottom();
                    }
                }
            })
            .catch(err => console.error(err));
    }

    function fetchUnread() {
        if(isChatOpen) return;
        fetch('<?php echo BASE_URL; ?>chat/getUnreadCount')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success' && res.count > 0) {
                    unreadBadge.textContent = res.count;
                    unreadBadge.classList.remove('hidden');
                } else {
                    unreadBadge.classList.add('hidden');
                }
            });
    }

    // Send Message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if(!text) return;
        
        chatInput.value = '';
        
        const formData = new FormData();
        formData.append('message', text);

        fetch('<?php echo BASE_URL; ?>chat/sendMessage', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(res => {
            if(res.status === 'success') {
                fetchMessages(); // Immediately fetch to update view
            }
        });
    });

    // Unread polling when closed
    function pollUnread() {
        setInterval(fetchUnread, 3000);
    }
    
    // Initial fetch if we want badge
    fetchUnread();
    // Start background unread poll
    pollUnread();

    // Add a simple fade-in-up animation CSS
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease forwards; }
    `;
    document.head.appendChild(style);
});
</script>
<?php endif; ?>
