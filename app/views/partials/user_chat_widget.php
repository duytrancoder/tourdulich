<?php if (!empty($_SESSION['login'])): ?>
<!-- Chat Widget - Pure CSS, no Tailwind dependency -->
<style>
#chat-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
#chat-toggle {
    position: relative;
    width: 56px;
    height: 56px;
    background: #014d4e;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(1, 77, 78, 0.4);
    transition: transform 0.2s ease, background 0.2s ease;
    outline: none;
}
#chat-toggle:hover { background: #013839; transform: scale(1.08); }
#chat-toggle:active { transform: scale(0.95); }
#chat-icon-open, #chat-icon-close {
    position: absolute;
    transition: opacity 0.25s ease, transform 0.25s ease;
}
#chat-icon-close { opacity: 0; transform: rotate(-90deg); }
#chat-toggle.is-open #chat-icon-open { opacity: 0; transform: rotate(90deg); }
#chat-toggle.is-open #chat-icon-close { opacity: 1; transform: rotate(0deg); }
#unread-badge {
    display: none;
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    border: 2px solid #fff;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
}
#unread-badge.visible { display: flex; }
#chat-window {
    display: none;
    flex-direction: column;
    position: absolute;
    bottom: 68px;
    right: 0;
    width: 320px;
    height: 400px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 12px 48px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transform-origin: bottom right;
    transform: scale(0.92) translateY(8px);
    opacity: 0;
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease;
}
#chat-window.is-open {
    display: flex;
    transform: scale(1) translateY(0);
    opacity: 1;
}
.chat-header {
    background: #014d4e;
    color: #fff;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(1,77,78,0.2);
}
.chat-header-left { display: flex; align-items: center; gap: 10px; }
.chat-header-avatar {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.chat-header h3 {
    margin: 0; font-size: 14px; font-weight: 700; line-height: 1.3;
    color: #fff !important;
}
.chat-header p {
    margin: 0; font-size: 11px; color: rgba(255,255,255,0.75); line-height: 1;
}
.chat-close-btn {
    background: none; border: none; cursor: pointer;
    color: rgba(255,255,255,0.75); padding: 4px;
    border-radius: 6px; transition: color 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.chat-close-btn:hover { color: #fff; }
#chat-messages {
    flex: 1;
    padding: 14px;
    overflow-y: auto;
    background: #f8fafb;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
#chat-messages::-webkit-scrollbar { width: 4px; }
#chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
.chat-start-label {
    text-align: center;
    font-size: 11px;
    color: #9ca3af;
    margin: 4px 0;
}
.chat-msg-wrap {
    display: flex;
    flex-direction: column;
    max-width: 85%;
    animation: msgFadeIn 0.25s ease forwards;
}
.chat-msg-wrap.from-user { align-self: flex-end; align-items: flex-end; }
.chat-msg-wrap.from-admin { align-self: flex-start; align-items: flex-start; }
.chat-bubble {
    padding: 8px 13px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.5;
    word-break: break-word;
}
.from-user .chat-bubble {
    background: #014d4e; color: #fff;
    border-bottom-right-radius: 4px;
}
.from-admin .chat-bubble {
    background: #fff; color: #1f2937;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.chat-time {
    font-size: 10px;
    color: #9ca3af;
    margin-top: 3px;
    padding: 0 3px;
}
.chat-input-area {
    padding: 10px 12px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
    flex-shrink: 0;
}
.chat-form { display: flex; gap: 8px; align-items: center; }
.chat-form input {
    flex: 1;
    background: #f1f5f9;
    border: 1.5px solid transparent;
    border-radius: 24px;
    padding: 8px 16px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s, background 0.2s;
    color: #1f2937;
}
.chat-form input:focus {
    background: #fff;
    border-color: #014d4e;
    box-shadow: 0 0 0 3px rgba(1,77,78,0.1);
}
.chat-form button {
    width: 36px; height: 36px;
    background: #014d4e; color: #fff;
    border: none; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s, transform 0.15s;
}
.chat-form button:hover { background: #013839; }
.chat-form button:active { transform: scale(0.9); }
@keyframes msgFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div id="chat-widget">
    <!-- Chat Window -->
    <div id="chat-window">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-header-avatar">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <h3>Hỗ trợ trực tuyến</h3>
                    <p>Chúng tôi sẵn sàng giúp đỡ</p>
                </div>
            </div>
            <button id="close-chat" class="chat-close-btn" aria-label="Đóng">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Messages -->
        <div id="chat-messages">
            <div class="chat-start-label">Bắt đầu cuộc trò chuyện</div>
        </div>

        <!-- Input -->
        <div class="chat-input-area">
            <form id="chat-form" class="chat-form" autocomplete="off">
                <input type="text" id="chat-input" placeholder="Nhập tin nhắn..." required>
                <button type="submit" aria-label="Gửi">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Bubble -->
    <button id="chat-toggle" aria-label="Mở chat hỗ trợ">
        <span id="unread-badge">0</span>
        <svg id="chat-icon-open" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg id="chat-icon-close" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

<script>
(function() {
    var toggle = document.getElementById('chat-toggle');
    var closeBtn = document.getElementById('close-chat');
    var chatWindow = document.getElementById('chat-window');
    var badge = document.getElementById('unread-badge');
    var form = document.getElementById('chat-form');
    var input = document.getElementById('chat-input');
    var messages = document.getElementById('chat-messages');
    var isOpen = false;
    var lastId = 0;
    var pollTimer = null;
    var unreadTimer = null;
    var BASE = '<?php echo BASE_URL; ?>';

    function openChat() {
        isOpen = true;
        chatWindow.classList.add('is-open');
        toggle.classList.add('is-open');
        badge.classList.remove('visible');
        fetchMessages();
        pollTimer = setInterval(fetchMessages, 3000);
    }

    function closeChat() {
        isOpen = false;
        chatWindow.classList.remove('is-open');
        toggle.classList.remove('is-open');
        clearInterval(pollTimer);
        startUnreadPoll();
    }

    toggle.addEventListener('click', function() { isOpen ? closeChat() : openChat(); });
    closeBtn.addEventListener('click', closeChat);

    // Intercept "Yêu cầu hỗ trợ" nav link
    document.querySelectorAll('a[href$="issue"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            isOpen ? closeChat() : openChat();
        });
    });

    function formatTime(str) {
        var d = new Date(str);
        return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    function appendMsg(msg) {
        var isUser = msg.Sender === 'user';
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg-wrap ' + (isUser ? 'from-user' : 'from-admin');
        wrap.innerHTML =
            '<div class="chat-bubble">' + escHtml(msg.Message) + '</div>' +
            '<span class="chat-time">' + formatTime(msg.CreatedAt) + '</span>';
        messages.appendChild(wrap);
        lastId = Math.max(lastId, parseInt(msg.id));
        messages.scrollTop = messages.scrollHeight;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function fetchMessages() {
        fetch(BASE + 'chat/getMessages')
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.status === 'success') {
                    res.data.filter(function(m){ return parseInt(m.id) > lastId; })
                        .forEach(appendMsg);
                }
            }).catch(function(){});
    }

    function fetchUnread() {
        if (isOpen) return;
        fetch(BASE + 'chat/getUnreadCount')
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.status === 'success' && res.count > 0) {
                    badge.textContent = res.count;
                    badge.classList.add('visible');
                } else {
                    badge.classList.remove('visible');
                }
            }).catch(function(){});
    }

    function startUnreadPoll() {
        clearInterval(unreadTimer);
        unreadTimer = setInterval(fetchUnread, 3000);
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        var fd = new FormData();
        fd.append('message', text);
        fetch(BASE + 'chat/sendMessage', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(res) { if (res.status === 'success') fetchMessages(); })
            .catch(function(){});
    });

    fetchUnread();
    startUnreadPoll();
})();
</script>
<?php endif; ?>
