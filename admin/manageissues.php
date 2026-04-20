<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {
    header('location:index.php');
    exit;
}

$pageTitle = "GoTravel Admin | Chat trực tuyến";
$currentPage = 'chat';
include('includes/layout-start.php');
?>
<!-- Ensure Tailwind CSS is loaded for this specific page -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="h-[calc(100vh-80px)] flex flex-col p-4 bg-gray-50 font-sans">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-1 overflow-hidden">
        
        <!-- Left Column: User List -->
        <div class="w-1/3 border-r border-gray-100 flex flex-col">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-bold text-gray-800 text-lg">Danh sách hội thoại</h2>
            </div>
            <div id="user-list" class="flex-1 overflow-y-auto">
                <!-- User items will be injected here -->
                <div class="p-4 text-center text-gray-400 text-sm">Đang tải...</div>
            </div>
        </div>

        <!-- Right Column: Chat Window -->
        <div class="flex-1 flex flex-col bg-gray-50/50">
            <!-- Chat Header -->
            <div id="chat-header" class="p-4 border-b border-gray-100 bg-white flex items-center justify-between hidden">
                <div>
                    <h3 id="chat-user-name" class="font-bold text-gray-800"></h3>
                    <p id="chat-user-email" class="text-xs text-gray-500"></p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="chat-empty" class="flex-1 flex items-center justify-center text-gray-400 flex-col gap-2">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span>Chọn một khách hàng để bắt đầu chat</span>
            </div>

            <!-- Messages Area -->
            <div id="chat-messages" class="flex-1 p-6 overflow-y-auto flex flex-col gap-4 hidden">
                <!-- Messages injected here -->
            </div>

            <!-- Input Area -->
            <div id="chat-input-area" class="p-4 bg-white border-t border-gray-100 hidden">
                <form id="admin-chat-form" class="flex gap-2 relative">
                    <input type="text" id="admin-chat-input" class="flex-1 bg-gray-100 border-transparent rounded-full px-5 py-3 text-sm focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none transition-all" placeholder="Nhập tin nhắn phản hồi..." autocomplete="off" required>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white rounded-full w-11 h-11 flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userListEl = document.getElementById('user-list');
    const chatHeader = document.getElementById('chat-header');
    const chatEmpty = document.getElementById('chat-empty');
    const chatMessages = document.getElementById('chat-messages');
    const chatInputArea = document.getElementById('chat-input-area');
    const chatForm = document.getElementById('admin-chat-form');
    const chatInput = document.getElementById('admin-chat-input');
    const chatUserName = document.getElementById('chat-user-name');
    const chatUserEmail = document.getElementById('chat-user-email');

    let currentEmail = null;
    let lastMessageId = 0;
    let userList = [];

    // Format time helper
    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' - ' + d.toLocaleDateString();
    }

    // Load User List
    function fetchUsers() {
        fetch('<?php echo BASE_URL; ?>chat/adminGetListUsers')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    userList = res.data;
                    renderUserList();
                }
            })
            .catch(err => console.error(err));
    }

    function renderUserList() {
        if (userList.length === 0) {
            userListEl.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">Chưa có cuộc trò chuyện nào.</div>';
            return;
        }

        userListEl.innerHTML = userList.map(u => `
            <div class="user-item cursor-pointer p-4 border-b border-gray-50 hover:bg-teal-50 transition-colors ${currentEmail === u.EmailId ? 'bg-teal-50 border-l-4 border-l-teal-600' : 'border-l-4 border-l-transparent'}" data-email="${u.EmailId}" data-name="${u.FullName}">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-semibold text-gray-800 truncate pr-2 text-sm">${u.FullName}</h4>
                    <span class="text-[10px] text-gray-400 whitespace-nowrap">${formatTime(u.LastMessageTime).split(' - ')[0]}</span>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500 truncate pr-2">${u.EmailId}</p>
                    ${parseInt(u.UnreadCount) > 0 ? `<span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">${u.UnreadCount}</span>` : ''}
                </div>
            </div>
        `).join('');

        // Attach click events
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function() {
                const email = this.getAttribute('data-email');
                const name = this.getAttribute('data-name');
                openChat(email, name);
            });
        });
    }

    // Open chat for specific user
    function openChat(email, name) {
        currentEmail = email;
        lastMessageId = 0; // Reset last message ID for new conversation
        
        chatUserName.textContent = name;
        chatUserEmail.textContent = email;
        
        chatEmpty.classList.add('hidden');
        chatHeader.classList.remove('hidden');
        chatMessages.classList.remove('hidden');
        chatInputArea.classList.remove('hidden');
        
        chatMessages.innerHTML = '<div class="text-center text-gray-400 text-sm mt-4">Đang tải tin nhắn...</div>';
        
        fetchMessages(true);
        renderUserList(); // Update selected state in list
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function appendMessage(msg) {
        const isAdmin = msg.Sender === 'admin';
        const alignClass = isAdmin ? 'self-end flex-row-reverse' : 'self-start';
        const bgClass = isAdmin ? 'bg-teal-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-200 shadow-sm rounded-bl-none';
        
        const html = `
            <div class="flex flex-col ${alignClass} max-w-[70%]">
                <div class="${bgClass} px-4 py-2.5 rounded-2xl text-[14px] leading-relaxed break-words">
                    ${msg.Message}
                </div>
                <div class="text-[10px] text-gray-400 mt-1 px-1 ${isAdmin ? 'text-right' : 'text-left'}">
                    ${formatTime(msg.CreatedAt)}
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', html);
        lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
    }

    function fetchMessages(isInitial = false) {
        if (!currentEmail) return;

        fetch(`<?php echo BASE_URL; ?>chat/adminGetMessages?email=${encodeURIComponent(currentEmail)}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (isInitial) chatMessages.innerHTML = ''; // Clear loader
                    
                    const newMessages = res.data.filter(m => parseInt(m.id) > lastMessageId);
                    if (newMessages.length > 0) {
                        newMessages.forEach(appendMessage);
                        scrollToBottom();
                    }
                }
            })
            .catch(err => console.error(err));
    }

    // Send Message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if(!text || !currentEmail) return;
        
        chatInput.value = '';
        
        const formData = new FormData();
        formData.append('email', currentEmail);
        formData.append('message', text);

        fetch('<?php echo BASE_URL; ?>chat/adminSendMessage', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(res => {
            if(res.status === 'success') {
                fetchMessages(); // Immediately fetch
                fetchUsers(); // Update list (last message time)
            }
        });
    });

    // Polling Intervals
    setInterval(() => {
        fetchUsers();
        if (currentEmail) fetchMessages();
    }, 3000);

    // Initial load
    fetchUsers();
});
</script>

<?php include('includes/layout-end.php');?>
