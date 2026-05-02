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
                                <span class="ai-stars">✨</span> Perfil Pro
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

            {{-- ── RIGHT: AI REAL ESTATE PANEL (REDESIGNED) ── --}}
            <transition name="slide-right">
                <aside v-if="showQualification && selectedChat" class="analysis-panel custom-scrollbar">
                    
                    {{-- Bloque 1: DATOS DEL PROSPECTO --}}
                    <div class="panel-section">
                        <div class="section-header">
                            <i class="section-icon user-icon"></i>
                            <h3>DATOS DEL PROSPECTO</h3>
                        </div>
                        
                        <div class="form-grid">
                            <div class="input-group full">
                                <label>NOMBRE COMPLETO *</label>
                                <input type="text" v-model="selectedChat.lead_name" placeholder="Nombre del cliente">
                            </div>
                            <div class="input-group">
                                <label>TELÉFONO *</label>
                                <div class="input-with-icon">
                                    <span class="icon">📞</span>
                                    <input type="text" v-model="selectedChat.remote_jid" disabled>
                                </div>
                            </div>
                            <div class="input-group full">
                                <label>CORREO ELECTRÓNICO</label>
                                <div class="input-with-icon">
                                    <span class="icon">✉️</span>
                                    <input type="email" v-model="selectedChat.email" placeholder="email@ejemplo.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque 2: GESTIÓN DE VENTA --}}
                    <div class="panel-section">
                        <div class="section-header">
                            <i class="section-icon flash-icon"></i>
                            <h3>GESTIÓN DE VENTA</h3>
                        </div>
                        
                        <div class="form-grid">
                            <div class="input-group">
                                <label>PROYECTO INTERÉS</label>
                                <select v-model="selectedChat.project_interest">
                                    <option value="">Seleccionar...</option>
                                    <option>Residencial Primavera</option>
                                    <option>Torre Ejecutiva</option>
                                    <option>Hacienda Real</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>ASESOR ASIGNADO</label>
                                <select v-model="selectedChat.user_id">
                                    <option value="">Sin asignar</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="input-group full">
                                <label>ETAPA DEL PIPELINE</label>
                                <select v-model="selectedChat.stage">
                                    <option>Nuevo Prospecto</option>
                                    <option>Cualificado por IA</option>
                                    <option>Cita Programada</option>
                                    <option>Cierre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque 3: PERFIL ECONÓMICO (AI POWERED) --}}
                    <div class="panel-section ai-highlight">
                        <div class="section-header">
                            <i class="section-icon dollar-icon"></i>
                            <h3>PERFIL ECONÓMICO</h3>
                        </div>
                        
                        <div class="form-grid">
                            <div class="input-group">
                                <label>CANAL ORIGEN</label>
                                <select v-model="selectedChat.source">
                                    <option>WhatsApp AI</option>
                                    <option>Facebook Ads</option>
                                    <option>Instagram</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>PRESUPUESTO</label>
                                <div class="input-with-icon currency">
                                    <span class="icon">$</span>
                                    <input type="text" v-model="selectedChat.budget_detected" placeholder="0.00">
                                </div>
                            </div>
                            <div class="input-group full">
                                <label>DETALLES DE INTERÉS</label>
                                <textarea v-model="selectedChat.interest_details" placeholder="Ej: Busca departamento de 3 dormitorios con vista al parque..."></textarea>
                            </div>
                            <div class="input-group full">
                                <label>ETIQUETAS (SEPARAR POR COMA)</label>
                                <div class="input-with-icon tag">
                                    <span class="icon">🏷️</span>
                                    <input type="text" placeholder="VIP, CALIENTE, INVERSIONISTA...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-actions">
                        <button class="btn-primary-gradient" @@click="saveLead">
                            Actualizar Expediente
                        </button>
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
            background: #0b0f1a;
            color: #f8fafc;
        }

        .mesh-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0b0f1a 100%);
            z-index: 0;
        }

        .main-layout {
            position: relative;
            z-index: 1;
            display: flex;
            height: 100%;
            padding: 16px;
            gap: 16px;
        }

        /* Sidebar Glass */
        .sidebar-glass {
            width: 300px;
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header { padding: 20px; }
        .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .logo-orb { width: 24px; height: 24px; background: #9333ea; border-radius: 50%; box-shadow: 0 0 15px #9333ea; }
        .brand-name { font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .brand-name span { color: #9333ea; }

        .search-wrapper {
            background: #111827;
            border-radius: 10px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .search-wrapper input { background: transparent; border: none; color: white; outline: none; font-size: 13px; width: 100%; }

        /* Chat Cards */
        .chat-list { flex: 1; overflow-y: auto; padding: 10px; }
        .chat-card {
            padding: 12px;
            border-radius: 12px;
            display: flex;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 6px;
        }
        .chat-card:hover { background: rgba(255, 255, 255, 0.03); }
        .chat-card.active { background: rgba(147, 51, 234, 0.1); border: 1px solid rgba(147, 51, 234, 0.2); }

        .chat-avatar { position: relative; width: 40px; height: 40px; background: #1f2937; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .chat-info { flex: 1; min-width: 0; }
        .chat-top { display: flex; justify-content: space-between; align-items: center; }
        .phone { font-weight: 600; font-size: 13px; color: #e5e7eb; }
        .score-tag { font-size: 9px; font-weight: 800; color: #10b981; }
        .last-msg { font-size: 11px; color: #6b7280; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Main Chat Area */
        .chat-main-glass {
            flex: 1;
            background: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header { padding: 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); display: flex; justify-content: space-between; align-items: center; background: rgba(17, 24, 39, 0.6); }
        .header-user { display: flex; align-items: center; gap: 10px; }
        .status-indicator { width: 8px; height: 8px; border-radius: 50%; background: #10b981; }
        .intent-pill { font-size: 9px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; background: rgba(255, 255, 255, 0.05); margin-top: 4px; }

        .btn-ai-toggle { background: #1f2937; border: 1px solid #374151; color: #d1d5db; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; }
        .btn-ai-toggle.active { background: #9333ea; border-color: #a855f7; color: white; }

        /* Bubbles */
        .message-thread { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        .msg-bubble-wrap { display: flex; width: 100%; }
        .msg-bubble-wrap.user { justify-content: flex-start; }
        .msg-bubble-wrap.bot { justify-content: flex-end; }
        .msg-bubble { max-width: 80%; padding: 10px 16px; border-radius: 12px; font-size: 13px; line-height: 1.4; }
        .user .msg-bubble { background: #1f2937; color: #e5e7eb; }
        .bot .msg-bubble { background: #9333ea; color: white; }
        .msg-time { font-size: 9px; opacity: 0.5; display: block; margin-top: 4px; }

        /* Input Area */
        .chat-input-area { padding: 16px 20px; }
        .input-glass-wrap { background: #111827; border-radius: 12px; padding: 6px 6px 6px 16px; display: flex; align-items: center; border: 1px solid #374151; }
        .input-glass-wrap input { flex: 1; background: transparent; border: none; color: white; outline: none; font-size: 13px; }
        .send-btn { width: 36px; height: 36px; background: #9333ea; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; }

        /* ── ANALYSIS PANEL (RIGHT) ── */
        .analysis-panel {
            width: 360px;
            background: #111827;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .panel-section {
            background: #1f2937;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .section-header h3 {
            font-size: 12px;
            font-weight: 800;
            color: #d1d5db;
            margin: 0;
            letter-spacing: 1px;
        }

        .section-icon { width: 16px; height: 16px; display: inline-block; background-size: contain; background-repeat: no-repeat; }
        .user-icon { filter: invert(36%) sepia(94%) saturate(1914%) hue-rotate(243deg) brightness(96%) contrast(105%); background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' /%3E%3C/svg%3E"); }
        .flash-icon { filter: invert(36%) sepia(94%) saturate(1914%) hue-rotate(243deg) brightness(96%) contrast(105%); background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z' /%3E%3C/svg%3E"); }
        .dollar-icon { filter: invert(36%) sepia(94%) saturate(1914%) hue-rotate(243deg) brightness(96%) contrast(105%); background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' /%3E%3C/svg%3E"); }

        .form-grid { display: flex; flex-direction: column; gap: 14px; }
        .input-group label { display: block; font-size: 10px; font-weight: 800; color: #9ca3af; margin-bottom: 6px; }
        
        .input-group input, .input-group select, .input-group textarea {
            width: 100%;
            background: #111827;
            border: 1px solid #374151;
            border-radius: 10px;
            padding: 10px 12px;
            color: white;
            font-size: 13px;
            outline: none;
        }
        
        .input-with-icon { position: relative; }
        .input-with-icon .icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5; font-size: 14px; }
        .input-with-icon input { padding-left: 36px; }

        .btn-primary-gradient {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #9333ea, #7e22ce);
            color: white;
            border-radius: 12px;
            border: none;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-primary-gradient:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4); }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
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
                    },
                    loadMessages(id) {
                        axios.get('/admin/whatsapp/api/messages/' + id).then(res => {
                            const oldLen = this.messages.length;
                            this.messages = res.data;
                            if (res.data.length > oldLen) this.scrollToBottom();
                        });
                    },
                    saveLead() {
                        alert('Frontend Listo: Los datos se guardarán cuando activemos la base de datos completa.');
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
