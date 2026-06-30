<style>
#wt-chatbot-fab {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #0284c7;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(179,138,90,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9100;
    transition: transform .18s, box-shadow .18s;
    color: #fff;
}
#wt-chatbot-fab:hover { transform: scale(1.08); box-shadow: 0 6px 22px rgba(179,138,90,.55); }
#wt-chatbot-fab .fab-icon-chat { display: flex; }
#wt-chatbot-fab .fab-icon-close { display: none; }
#wt-chatbot-fab.open .fab-icon-chat { display: none; }
#wt-chatbot-fab.open .fab-icon-close { display: flex; }

#wt-chatbot-window {
    position: fixed;
    bottom: 5.5rem;
    right: 1.5rem;
    width: 360px;
    max-height: 520px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    display: none;
    flex-direction: column;
    z-index: 9099;
    overflow: hidden;
    font-family: 'Inter', sans-serif;
    animation: wtChatSlideIn .2s ease;
}
@keyframes wtChatSlideIn {
    from { opacity: 0; transform: translateY(16px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
#wt-chatbot-window.open { display: flex; }

.wt-chatbot-header {
    background: #0284c7;
    color: #fff;
    padding: .85rem 1rem;
    display: flex;
    align-items: center;
    gap: .65rem;
    flex-shrink: 0;
}
.wt-chatbot-header-icon {
    width: 32px; height: 32px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wt-chatbot-header-info { flex: 1; }
.wt-chatbot-header-title { font-size: .9rem; font-weight: 600; line-height: 1.2; }
.wt-chatbot-header-sub { font-size: .72rem; opacity: .8; }
.wt-chatbot-clear-btn {
    background: rgba(255,255,255,.15);
    border: none;
    color: #fff;
    border-radius: 6px;
    padding: .28rem .5rem;
    font-size: .7rem;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}
.wt-chatbot-clear-btn:hover { background: rgba(255,255,255,.28); }

.wt-chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: .85rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .65rem;
    min-height: 200px;
    scroll-behavior: smooth;
}
.wt-chatbot-messages::-webkit-scrollbar { width: 4px; }
.wt-chatbot-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

.wt-chat-msg {
    max-width: 85%;
    padding: .55rem .8rem;
    border-radius: 12px;
    font-size: .83rem;
    line-height: 1.5;
    word-break: break-word;
}
.wt-chat-msg.user {
    background: #0284c7;
    color: #fff;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}
.wt-chat-msg.assistant {
    background: #f1f5f9;
    color: #1e293b;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}
.wt-chat-msg.error {
    background: #fef2f2;
    color: #dc2626;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    font-size: .78rem;
}
.wt-chat-typing {
    align-self: flex-start;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: .55rem .8rem;
    background: #f1f5f9;
    border-radius: 12px;
    border-bottom-left-radius: 4px;
}
.wt-chat-typing span {
    width: 6px; height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: wtTypingBounce 1.2s infinite ease-in-out;
}
.wt-chat-typing span:nth-child(2) { animation-delay: .2s; }
.wt-chat-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes wtTypingBounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-6px); }
}

.wt-chatbot-footer {
    padding: .75rem 1rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: .5rem;
    flex-shrink: 0;
    background: #fff;
}
#wt-chatbot-input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: .5rem .75rem;
    font-size: .83rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    resize: none;
    line-height: 1.45;
    max-height: 100px;
    overflow-y: auto;
    transition: border-color .15s;
}
#wt-chatbot-input:focus { border-color: #0284c7; }
#wt-chatbot-input::placeholder { color: #94a3b8; }
#wt-chatbot-send-btn {
    width: 36px; height: 36px;
    background: #0284c7;
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    align-self: flex-end;
    transition: background .15s, transform .1s;
}
#wt-chatbot-send-btn:hover { background: #0369a1; }
#wt-chatbot-send-btn:active { transform: scale(.93); }
#wt-chatbot-send-btn:disabled { background: #d4b896; cursor: not-allowed; }

.wt-chat-msg pre {
    background: rgba(0,0,0,.07);
    border-radius: 6px;
    padding: .4rem .6rem;
    margin: .3rem 0 0;
    font-size: .78rem;
    overflow-x: auto;
    white-space: pre-wrap;
}
.wt-chat-msg.user pre { background: rgba(255,255,255,.15); }

@media (max-width: 480px) {
    #wt-chatbot-window {
        width: calc(100vw - 2rem);
        right: 1rem;
        bottom: 5rem;
    }
}
</style>

<button id="wt-chatbot-fab" aria-label="Open WT Assistant" onclick="wtToggleChatbot()">
    <span class="fab-icon-chat">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </span>
    <span class="fab-icon-close">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </span>
</button>

<div id="wt-chatbot-window">
    <div class="wt-chatbot-header">
        <div class="wt-chatbot-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="wt-chatbot-header-info">
            <div class="wt-chatbot-header-title">WT Assistant</div>
            <div class="wt-chatbot-header-sub">Powered by Ollama · Local AI</div>
        </div>
        <button class="wt-chatbot-clear-btn" onclick="wtClearChatHistory()" title="Clear conversation">Clear</button>
    </div>
    <div class="wt-chatbot-messages" id="wt-chatbot-messages">
        <div class="wt-chat-msg assistant">Hi {{ Auth::guard('wt')->user()->full_name ? explode(' ', Auth::guard('wt')->user()->full_name)[0] : 'there' }}! I'm your WT Assistant. Ask me anything about walkie talkie inventory, requests, or maintenance.</div>
    </div>
    <div class="wt-chatbot-footer">
        <textarea id="wt-chatbot-input" placeholder="Ask anything…" rows="1"></textarea>
        <button id="wt-chatbot-send-btn" onclick="wtSendChatMessage()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
    </div>
</div>

<script>
(function() {
    const CHAT_URL   = '{{ route("chatbot.message") }}';
    const CLEAR_URL  = '{{ route("chatbot.clear") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    window.wtToggleChatbot = function() {
        const fab = document.getElementById('wt-chatbot-fab');
        const win = document.getElementById('wt-chatbot-window');
        const isOpen = win.classList.contains('open');
        if (isOpen) {
            win.classList.remove('open');
            fab.classList.remove('open');
        } else {
            win.classList.add('open');
            fab.classList.add('open');
            document.getElementById('wt-chatbot-input').focus();
            wtScrollToBottom();
        }
    };

    window.wtSendChatMessage = async function() {
        const input = document.getElementById('wt-chatbot-input');
        const sendBtn = document.getElementById('wt-chatbot-send-btn');
        const message = input.value.trim();
        if (!message) return;

        wtAppendMessage('user', wtEscapeHtml(message));
        input.value = '';
        wtAutoResize(input);

        const typingEl = wtShowTyping();
        sendBtn.disabled = true;

        try {
            const res = await fetch(CHAT_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ message, system: 'wt' }),
            });

            typingEl.remove();
            const data = await res.json();

            if (!res.ok || data.error) {
                wtAppendMessage('error', 'âš  ' + (data.error || 'Something went wrong.'));
            } else {
                wtAppendMessage('assistant', wtFormatMessage(data.reply));
            }
        } catch (err) {
            typingEl.remove();
            wtAppendMessage('error', 'âš  Network error. Is Ollama running?');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    };

    window.wtClearChatHistory = async function() {
        await fetch(CLEAR_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
        document.getElementById('wt-chatbot-messages').innerHTML =
            '<div class="wt-chat-msg assistant">Conversation cleared. How can I help you?</div>';
    };

    function wtAppendMessage(role, html) {
        const msgs = document.getElementById('wt-chatbot-messages');
        const div = document.createElement('div');
        div.className = 'wt-chat-msg ' + role;
        div.innerHTML = html;
        msgs.appendChild(div);
        wtScrollToBottom();
    }

    function wtShowTyping() {
        const msgs = document.getElementById('wt-chatbot-messages');
        const div = document.createElement('div');
        div.className = 'wt-chat-typing';
        div.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(div);
        wtScrollToBottom();
        return div;
    }

    function wtScrollToBottom() {
        const msgs = document.getElementById('wt-chatbot-messages');
        setTimeout(() => { msgs.scrollTop = msgs.scrollHeight; }, 30);
    }

    function wtEscapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                  .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function wtFormatMessage(text) {
        text = wtEscapeHtml(text);
        text = text.replace(/```([\s\S]*?)```/g, '<pre>$1</pre>');
        text = text.replace(/`([^`]+)`/g, '<code style="background:rgba(0,0,0,.08);padding:.1em .3em;border-radius:3px;font-size:.85em">$1</code>');
        text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    function wtAutoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('wt-chatbot-input');
        if (!input) return;
        input.addEventListener('input', function() { wtAutoResize(this); });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); wtSendChatMessage(); }
        });
    });
})();
</script>
