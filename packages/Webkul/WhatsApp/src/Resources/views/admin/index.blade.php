<x-admin::layouts>
    <x-slot:title>
        {{ __('Prexup AI · WhatsApp Hub') }}
    </x-slot>

    <div id="whatsapp-chat-app" class="prexup-container">
        
        {{-- ── MODERN BACKGROUND ── --}}
        <div class="mesh-bg"></div>

        <div class="main-layout">
            
            {{-- ── LEFT: CONVERSATIONS ── --}}
            <aside class="sidebar-glass">
                <div class="sidebar-header">
                    <div class="brand">
                        <div class="logo-orb"></div>
                        <h2 class="brand-name">Prexup<span>AI</span></h2>
                    </div>
                    <div class="search-wrapper">
                        <i class="icon search-icon"></i>
                        <input type="text" v-model="search" placeholder="Filtrar clientes...">
                    </div>
                </div>

                <div class="chat-list custom-scrollbar">
                    <div 
                        v-for="chat in filteredConversations" 
                        :key="chat.id"
                        @@click="selectChat(chat)"
                        :class="['chat-card', selectedChat && selectedChat.id === chat.id ? 'active' : '', chat.intent_level]"
                    >
                        <div class="chat-avatar">
                            <span class="emoji">@{{ getIntentEmoji(chat.intent_level) }}</span>
                            <div v-if="chat.intent_level === 'ready_to_buy'" class="pulse-ring"></div>
                        </div>
                        <div class="chat-info">
                            <div class="chat-top">
                                <span class="phone">@{{ chat.remote_jid }}</span>
                                <span v-if="chat.ai_score > 0" class="score-tag">@{{ chat.ai_score }}%</span>
                            </div>
                            <p class="last-msg">@{{ chat.messages && chat.messages.length ? chat.messages[0].content : 'Iniciando IA...' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ── CENTER: CHAT WINDOW ── --}}
            <main class="chat-main-glass">
                <template v-if="selectedChat">
                    <header class="chat-header">
                        <div class="header-user">
                            <div class="status-indicator online"></div>
                            <div>
                                <h3>@{{ selectedChat.remote_jid }}</h3>
                                <div class="intent-pill" :class="selectedChat.intent_level">
                                    @{{ intentLabels[selectedChat.intent_level] || 'Analizando...' }}
                                </div>
                            </div>
                        </div>
                        <div class="header-actions">
                            <button @@click="showQualification = !showQualification" class="btn-ai-toggle" :class="{active: showQualification}">
                                <span class="ai-stars">✨</span> IA Insight
                            </button>
                        </div>
                    </header>

                    <div id="message-container" class="message-thread custom-scrollbar">
                        <div v-for="msg in messages" :key="msg.id" :class="['msg-bubble-wrap', msg.sender]">
                            <div class="msg-bubble">
                                <p>@{{ msg.content }}</p>
                                <span class="msg-time">@{{ formatTime(msg.created_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <footer class="chat-input-area">
                        <div class="input-glass-wrap">
                            <input type="text" v-model="newMessage" @@keyup.enter="send" placeholder="Mensaje manual para el cliente...">
                            <button @@click="send" :disabled="!newMessage || sending" class="send-btn">
                                <svg v-if="!sending" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M2.01 21L23 12L2.01 3L2 10l15 2l-15 2z"/></svg>
                                <span v-else class="loader"></span>
                            </button>
                        </div>
                    </footer>
                </template>
                <div v-else class="empty-state">
                    <div class="floating-orb"></div>
                    <h2>Panel de Control Inteligente</h2>
                    <p>Selecciona una conversación para empezar a monitorizar la IA.</p>
                </div>
            </main>

            {{-- ── RIGHT: AI ANALYSIS ── --}}
            <transition name="slide-right">
                <aside v-if="showQualification && selectedChat" class="analysis-panel">
                    <div class="panel-inner">
                        <h3>🧠 Análisis Cognitivo</h3>
                        
                        <div class="score-viz">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle" :stroke-dasharray="qualification.ai_score + ', 100'" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <text x="18" y="20.35" class="percentage">@{{ qualification.ai_score }}%</text>
                            </svg>
                            <p class="score-label">Probabilidad de Conversión</p>
                        </div>

                        <div class="insight-cards">
                            <div v-if="qualification.budget_detected" class="insight-card">
                                <span class="card-icon">💰</span>
                                <div>
                                    <label>Presupuesto</label>
                                    <p>@{{ qualification.budget_detected }}</p>
                                </div>
                            </div>
                            <div v-if="qualification.location_interest" class="insight-card">
                                <span class="card-icon">📍</span>
                                <div>
                                    <label>Ubicación</label>
                                    <p>@{{ qualification.location_interest }}</p>
                                </div>
                            </div>
                            <div v-if="qualification.property_type" class="insight-card">
                                <span class="card-icon">🏠</span>
                                <div>
                                    <label>Interés</label>
                                    <p>@{{ qualification.property_type }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="summary-box">
                            <label>Resumen de Perfil</label>
                            <p>@{{ qualification.qualification_summary || 'Generando resumen...' }}</p>
                        </div>

                        <div class="action-footer">
                            <button class="btn-create-opportunity">
                                Convertir en Oportunidad
                            </button>
                        </div>
                    </div>
                </aside>
            </transition>
        </div>
    </div>

    <style>
        /* ── PREXUP AI DESIGN SYSTEM ── */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

        .prexup-container {
            font-family: 'Outfit', sans-serif;
            position: relative;
            height: calc(100vh - 60px);
            width: 100%;
            overflow: hidden;
            background: #0f172a;
            color: #f8fafc;
        }

        .mesh-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            opacity: 0.8;
            z-index: 0;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .main-layout {
            position: relative;
            z-index: 1;
            display: flex;
            height: 100%;
            padding: 20px;
            gap: 20px;
        }

        /* Sidebar Glass */
        .sidebar-glass {
            width: 320px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header { padding: 24px; }
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .logo-orb { width: 32px; height: 32px; background: linear-gradient(135deg, #10b981, #6366f1); border-radius: 50%; box-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
        .brand-name { font-size: 24px; font-weight: 800; margin: 0; }
        .brand-name span { color: #10b981; }

        .search-wrapper {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .search-wrapper input { background: transparent; border: none; color: white; outline: none; font-size: 14px; width: 100%; }

        /* Chat Cards */
        .chat-list { flex: 1; overflow-y: auto; padding: 10px; }
        .chat-card {
            padding: 16px;
            border-radius: 16px;
            display: flex;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 8px;
            border: 1px solid transparent;
        }
        .chat-card:hover { background: rgba(255, 255, 255, 0.05); }
        .chat-card.active { background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.3); }

        .chat-avatar { position: relative; width: 48px; height: 48px; background: rgba(255, 255, 255, 0.05); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .chat-info { flex: 1; min-width: 0; }
        .chat-top { display: flex; justify-content: space-between; align-items: center; }
        .phone { font-weight: 600; font-size: 14px; }
        .score-tag { font-size: 10px; font-weight: 800; color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 2px 6px; border-radius: 6px; }
        .last-msg { font-size: 12px; color: #94a3b8; margin: 4px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Pulse for Hot Leads */
        .pulse-ring {
            position: absolute; width: 100%; height: 100%;
            border: 2px solid #ef4444; border-radius: 14px;
            animation: pulse 2s infinite;
        }
        @@keyframes pulse { 0% { transform: scale(0.9); opacity: 0.8; } 100% { transform: scale(1.3); opacity: 0; } }

        /* Main Chat Area */
        .chat-main-glass {
            flex: 1;
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header { padding: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); display: flex; justify-content: space-between; align-items: center; }
        .header-user { display: flex; align-items: center; gap: 12px; }
        .status-indicator { width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px #10b981; }
        .intent-pill { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 2px 10px; border-radius: 20px; display: inline-block; margin-top: 4px; }
        .intent-pill.cold { background: rgba(148, 163, 184, 0.2); color: #94a3b8; }
        .intent-pill.warm { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .intent-pill.hot { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .intent-pill.ready_to_buy { background: rgba(16, 185, 129, 0.2); color: #10b981; }

        .btn-ai-toggle { background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; padding: 8px 16px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-ai-toggle.active { background: #6366f1; color: white; box-shadow: 0 0 15px rgba(99, 102, 241, 0.4); }

        /* Bubbles */
        .message-thread { flex: 1; overflow-y: auto; padding: 30px; display: flex; flex-direction: column; gap: 16px; }
        .msg-bubble-wrap { display: flex; width: 100%; }
        .msg-bubble-wrap.user { justify-content: flex-start; }
        .msg-bubble-wrap.bot { justify-content: flex-end; }
        .msg-bubble { max-width: 70%; padding: 12px 20px; border-radius: 20px; position: relative; font-size: 14px; line-height: 1.5; }
        .user .msg-bubble { background: rgba(255, 255, 255, 0.05); color: #f1f5f9; border-bottom-left-radius: 4px; }
        .bot .msg-bubble { background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2); }
        .msg-time { font-size: 9px; opacity: 0.5; display: block; margin-top: 4px; text-align: right; }

        /* Input Area */
        .chat-input-area { padding: 24px; }
        .input-glass-wrap { background: rgba(15, 23, 42, 0.6); border-radius: 18px; padding: 8px 8px 8px 24px; display: flex; align-items: center; border: 1px solid rgba(255, 255, 255, 0.08); }
        .input-glass-wrap input { flex: 1; background: transparent; border: none; color: white; outline: none; padding: 10px 0; }
        .send-btn { width: 44px; height: 44px; background: #6366f1; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; transition: transform 0.2s; }
        .send-btn:hover { transform: scale(1.05); background: #4f46e5; }

        /* Analysis Panel */
        .analysis-panel { width: 340px; background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(12px); border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.1); padding: 30px; overflow-y: auto; }
        .analysis-panel h3 { margin: 0 0 30px; font-weight: 800; font-size: 18px; }

        .score-viz { text-align: center; margin-bottom: 40px; }
        .circular-chart { display: block; margin: 10px auto; max-width: 120px; max-height: 120px; }
        .circle-bg { fill: none; stroke: rgba(255, 255, 255, 0.05); stroke-width: 2.8; }
        .circle { fill: none; stroke-width: 2.8; stroke-linecap: round; stroke: #10b981; transition: stroke-dasharray 1s ease 0s; }
        .percentage { fill: white; font-weight: 800; font-size: 8px; text-anchor: middle; }
        .score-label { font-size: 12px; color: #94a3b8; font-weight: 600; }

        .insight-cards { display: grid; gap: 12px; margin-bottom: 30px; }
        .insight-card { background: rgba(15, 23, 42, 0.4); padding: 16px; border-radius: 16px; display: flex; gap: 12px; align-items: center; border: 1px solid rgba(255, 255, 255, 0.05); }
        .card-icon { font-size: 20px; }
        .insight-card label { display: block; font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; }
        .insight-card p { margin: 0; font-size: 14px; font-weight: 600; color: #f1f5f9; }

        .summary-box { background: rgba(99, 102, 241, 0.05); border-left: 4px solid #6366f1; padding: 16px; border-radius: 12px; margin-bottom: 30px; }
        .summary-box label { font-size: 10px; font-weight: 800; color: #818cf8; text-transform: uppercase; margin-bottom: 8px; display: block; }
        .summary-box p { font-size: 12px; line-height: 1.6; margin: 0; color: #cbd5e1; }

        .btn-create-opportunity { width: 100%; padding: 14px; background: #10b981; color: white; border-radius: 14px; border: none; font-weight: 800; cursor: pointer; transition: all 0.3s; }
        .btn-create-opportunity:hover { background: #059669; box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }

        /* Animations */
        .slide-right-enter-active, .slide-right-leave-active { transition: all 0.4s ease; }
        .slide-right-enter-from, .slide-right-leave-to { transform: translateX(50px); opacity: 0; }

        .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px; }
        .floating-orb { width: 120px; height: 120px; background: radial-gradient(circle, #6366f1, transparent); filter: blur(30px); animation: float 6s infinite ease-in-out; }
        @@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    </style>

    @pushOnce('scripts')
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            const { createApp } = Vue;

            createApp({
                data() {
                    return {
                        conversations: [],
                        messages: [],
                        qualification: {},
                        selectedChat: null,
                        search: '',
                        newMessage: '',
                        sending: false,
                        showQualification: true,
                        polling: null,
                        intentLabels: {
                            cold: '❄️ Explorador',
                            warm: '🌤 Interesado',
                            hot: '🔥 Muy Caliente',
                            ready_to_buy: '🚀 Cierre Inminente'
                        }
                    }
                },
                computed: {
                    filteredConversations() {
                        if (!this.search) return this.conversations;
                        return this.conversations.filter(c => c.remote_jid.includes(this.search));
                    }
                },
                mounted() {
                    this.loadConversations();
                    this.polling = setInterval(() => {
                        this.loadConversations();
                        if (this.selectedChat) this.loadMessages(this.selectedChat.id);
                    }, 4000);
                },
                unmounted() {
                    clearInterval(this.polling);
                },
                methods: {
                    loadConversations() {
                        axios.get('/admin/whatsapp/api/conversations').then(res => {
                            this.conversations = res.data;
                        });
                    },
                    selectChat(chat) {
                        this.selectedChat = chat;
                        this.loadMessages(chat.id);
                        this.loadQualification(chat.id);
                    },
                    loadMessages(id) {
                        axios.get('/admin/whatsapp/api/messages/' + id).then(res => {
                            const oldLen = this.messages.length;
                            this.messages = res.data;
                            if (res.data.length > oldLen) this.scrollToBottom();
                        });
                    },
                    loadQualification(id) {
                        axios.get('/admin/whatsapp/api/qualification/' + id).then(res => {
                            this.qualification = res.data;
                        });
                    },
                    send() {
                        if (!this.newMessage || this.sending) return;
                        this.sending = true;
                        axios.post('/admin/whatsapp/api/send', {
                            conversation_id: this.selectedChat.id,
                            content: this.newMessage
                        }).then(res => {
                            this.messages.push(res.data.message);
                            this.newMessage = '';
                            this.sending = false;
                            this.scrollToBottom();
                        }).catch(() => { this.sending = false; });
                    },
                    getIntentEmoji(level) {
                        const map = { cold: '❄️', warm: '🌤', hot: '🔥', ready_to_buy: '🚀' };
                        return map[level] || '🤖';
                    },
                    formatTime(d) {
                        if (!d) return '';
                        return new Date(d).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    },
                    scrollToBottom() {
                        setTimeout(() => {
                            const c = document.getElementById('message-container');
                            if (c) c.scrollTop = c.scrollHeight;
                        }, 100);
                    }
                }
            }).mount('#whatsapp-chat-app');
        </script>
    @endPushOnce
</x-admin::layouts>
