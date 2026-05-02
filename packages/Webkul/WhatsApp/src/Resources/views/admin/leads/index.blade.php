<x-admin::layouts>
    <x-slot:title>
        Gestión de Leads Pro
    </x-slot>

    <div id="leads-pro-app" class="leads-pro-container">
        {{-- ── HEADER ── --}}
        <header class="leads-header">
            <div class="header-left">
                <h1>Leads <span>Pro</span></h1>
                <div class="view-switcher">
                    <button :class="{active: view === 'kanban'}" @@click="view = 'kanban'">
                        <i class="icon-kanban"></i> Pipeline
                    </button>
                    <button :class="{active: view === 'list'}" @@click="view = 'list'">
                        <i class="icon-list"></i> Lista
                    </button>
                </div>
            </div>
            <div class="header-right">
                <button class="btn-new-lead" @@click="openModal">
                    <span class="plus-icon">+</span> Nuevo Prospecto
                </button>
            </div>
        </header>

        {{-- ── KANBAN VIEW ── --}}
        <div v-if="view === 'kanban'" class="kanban-board custom-scrollbar">
            <div v-for="column in board" :key="column.id" class="kanban-column">
                <div class="column-header">
                    <div class="header-info">
                        <span class="dot" :style="{background: column.color}"></span>
                        <h3>@{{ column.title }}</h3>
                    </div>
                    <span class="count">@{{ column.leads.length }}</span>
                </div>
                
                <div class="column-body">
                    <div v-for="lead in column.leads" :key="lead.id" class="lead-card">
                        <div class="card-top">
                            <span class="source-tag">WhatsApp AI</span>
                            <span class="time">2h</span>
                        </div>
                        <h4>@{{ lead.name }}</h4>
                        <div class="card-details">
                            <span class="budget">💰 @{{ lead.budget }}</span>
                        </div>
                        <div class="card-footer">
                            <div class="avatar-group">
                                <div class="avatar-sm">@{{ lead.name[0] }}</div>
                            </div>
                            <div class="intent-indicator" :class="lead.intent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── LIST VIEW ── --}}
        <div v-if="view === 'list'" class="list-view">
            <div class="table-glass">
                <table>
                    <thead>
                        <tr>
                            <th>Prospecto</th>
                            <th>Proyecto</th>
                            <th>Presupuesto</th>
                            <th>Origen</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lead in allLeads" :key="lead.id">
                            <td>
                                <div class="lead-info">
                                    <span class="lead-name">@{{ lead.name }}</span>
                                    <span class="lead-phone">@{{ lead.phone }}</span>
                                </div>
                            </td>
                            <td>@{{ lead.project || 'Pendiente' }}</td>
                            <td><span class="budget-text">@{{ lead.budget }}</span></td>
                            <td><span class="channel-pill">WhatsApp AI</span></td>
                            <td><span class="status-pill">@{{ lead.stage }}</span></td>
                            <td><button class="btn-action">...</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── MODAL: NUEVO PROSPECTO ── --}}
        <transition name="fade">
            <div v-if="showModal" class="modal-overlay" @@click.self="closeModal">
                <div class="modal-content-glass custom-scrollbar">
                    <div class="modal-header">
                        <div class="header-title">
                            <div class="plus-orb">+</div>
                            <div>
                                <h2>Nuevo prospecto</h2>
                                <p>Gestión comercial</p>
                            </div>
                        </div>
                        <button class="close-btn" @@click="closeModal">×</button>
                    </div>

                    <div class="modal-body">
                        {{-- BLOQUE 1: DATOS DEL PROSPECTO --}}
                        <div class="form-section">
                            <div class="section-title">
                                <i class="icon-user"></i> DATOS DEL PROSPECTO
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>NOMBRE COMPLETO *</label>
                                    <input type="text" placeholder="Nombre del cliente">
                                </div>
                                <div class="form-group">
                                    <label>TELÉFONO *</label>
                                    <div class="input-icon">
                                        <span>📞</span>
                                        <input type="text" placeholder="+51...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group full">
                                <label>CORREO ELECTRÓNICO</label>
                                <div class="input-icon">
                                    <span>✉️</span>
                                    <input type="email" placeholder="email@ejemplo.com">
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE 2: GESTIÓN DE VENTA --}}
                        <div class="form-section">
                            <div class="section-title">
                                <i class="icon-sale"></i> GESTIÓN DE VENTA
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>PROYECTO INTERÉS</label>
                                    <select>
                                        <option>Seleccionar...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ASESOR ASIGNADO</label>
                                    <select>
                                        <option>Sin asignar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group full">
                                <label>ETAPA DEL PIPELINE</label>
                                <select>
                                    <option>Seleccionar etapa...</option>
                                </select>
                            </div>
                        </div>

                        {{-- BLOQUE 3: PERFIL ECONÓMICO --}}
                        <div class="form-section highlight">
                            <div class="section-title">
                                <i class="icon-dollar"></i> PERFIL ECONÓMICO
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>CANAL ORIGEN</label>
                                    <select>
                                        <option>Seleccionar...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>PRESUPUESTO</label>
                                    <div class="input-icon money">
                                        <span class="currency">$</span>
                                        <input type="text" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>MONEDA</label>
                                    <select>
                                        <option>USD</option>
                                        <option>PEN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group full">
                                <label>DETALLES DE INTERÉS</label>
                                <textarea placeholder="Ej: Busca departamento de 3 dormitorios con vista al parque..."></textarea>
                            </div>
                            <div class="form-group full">
                                <label>ETIQUETAS (SEPARAR POR COMA)</label>
                                <div class="input-icon tag">
                                    <span>🏷️</span>
                                    <input type="text" placeholder="VIP, CALIENTE, INVERSIONISTA...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-cancel" @@click="closeModal">Cancelar</button>
                        <button class="btn-submit">
                            <span class="icon-save">💾</span> Crear
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

        .leads-pro-container {
            font-family: 'Outfit', sans-serif;
            background: #0b0f1a;
            color: #f8fafc;
            min-height: calc(100vh - 60px);
            padding: 24px;
        }

        /* HEADER */
        .leads-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header-left h1 { font-size: 28px; font-weight: 800; margin: 0 0 15px; }
        .header-left h1 span { color: #d946ef; }
        
        .view-switcher {
            display: flex;
            background: #1f2937;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }
        .view-switcher button {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .view-switcher button.active { background: #0b0f1a; color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }

        .btn-new-lead {
            background: linear-gradient(135deg, #d946ef, #9333ea);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* KANBAN */
        .kanban-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
            height: calc(100vh - 200px);
        }
        .kanban-column {
            min-width: 300px;
            max-width: 300px;
            background: rgba(31, 41, 55, 0.4);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .column-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-info { display: flex; align-items: center; gap: 10px; }
        .header-info h3 { font-size: 14px; font-weight: 800; margin: 0; color: #e5e7eb; }
        .dot { width: 8px; height: 8px; border-radius: 50%; }
        .count { background: #111827; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }

        .column-body { flex: 1; padding: 10px; overflow-y: auto; }
        .lead-card {
            background: #1f2937;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            cursor: move;
            transition: transform 0.2s;
        }
        .lead-card:hover { transform: translateY(-3px); border-color: rgba(147, 51, 234, 0.3); }
        .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .source-tag { font-size: 9px; font-weight: 800; color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 2px 6px; border-radius: 4px; }
        .time { font-size: 10px; color: #6b7280; }
        .lead-card h4 { font-size: 14px; font-weight: 700; margin: 0 0 10px; }
        .budget { font-size: 12px; font-weight: 600; color: #cbd5e1; }
        .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
        .avatar-sm { width: 24px; height: 24px; background: #9333ea; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; }
        .intent-indicator { width: 10px; height: 10px; border-radius: 50%; }
        .intent-indicator.hot { background: #ef4444; box-shadow: 0 0 10px #ef4444; }

        /* LIST VIEW */
        .table-glass {
            background: rgba(31, 41, 55, 0.4);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px; font-size: 12px; font-weight: 800; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.05); }
        td { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.03); }
        .lead-name { display: block; font-weight: 700; font-size: 14px; }
        .lead-phone { font-size: 12px; color: #6b7280; }
        .budget-text { font-weight: 800; color: #10b981; }

        /* MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
            z-index: 1000; display: flex; align-items: center; justify-content: center;
        }
        .modal-content-glass {
            width: 600px; max-height: 90vh; background: #111827;
            border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .modal-header { padding: 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .header-title { display: flex; align-items: center; gap: 15px; }
        .plus-orb { width: 44px; height: 44px; background: #d946ef; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; }
        .header-title h2 { margin: 0; font-size: 20px; font-weight: 800; }
        .header-title p { margin: 0; font-size: 12px; color: #6b7280; }
        .close-btn { background: transparent; border: none; color: white; font-size: 30px; cursor: pointer; opacity: 0.5; }

        .modal-body { padding: 30px; display: flex; flex-direction: column; gap: 30px; }
        .form-section { background: #1f2937; padding: 24px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.03); }
        .form-section.highlight { border: 1px solid rgba(217, 70, 239, 0.2); background: rgba(217, 70, 239, 0.05); }
        .section-title { font-size: 12px; font-weight: 800; color: #d946ef; margin-bottom: 20px; letter-spacing: 1px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full { grid-column: span 2; }
        .form-group label { display: block; font-size: 11px; font-weight: 800; color: #94a3b8; margin-bottom: 8px; }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: #111827; border: 1px solid #374151;
            border-radius: 12px; padding: 12px; color: white; font-size: 14px; outline: none;
        }
        .input-icon { position: relative; }
        .input-icon span { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5; }
        .input-icon input { padding-left: 40px; }

        .modal-footer { padding: 30px; display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid rgba(255,255,255,0.05); }
        .btn-cancel { background: transparent; border: none; color: #94a3b8; font-weight: 700; cursor: pointer; }
        .btn-submit { background: #d946ef; color: white; border: none; padding: 12px 30px; border-radius: 14px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(217, 70, 239, 0.4); display: flex; align-items: center; gap: 8px; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
    </style>

    @pushOnce('scripts')
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script>
            const { createApp } = Vue;
            createApp({
                data() {
                    return {
                        view: 'kanban',
                        showModal: false,
                        board: [
                            { id: 1, title: 'Nuevo prospecto', color: '#6366f1', leads: [{ id: 1, name: 'Juan Perez', budget: '150k', intent: 'hot' }] },
                            { id: 2, title: 'Cualificado IA', color: '#9333ea', leads: [{ id: 2, name: 'Maria Garcia', budget: '220k', intent: 'warm' }] },
                            { id: 3, title: 'Cita', color: '#d946ef', leads: [] },
                            { id: 4, title: 'Cierre', color: '#10b981', leads: [] }
                        ],
                        allLeads: [
                            { id: 1, name: 'Juan Perez', phone: '+51 987...', budget: '$150,000', stage: 'Nuevo', project: 'Primavera' }
                        ]
                    }
                },
                methods: {
                    openModal() { this.showModal = true; },
                    closeModal() { this.showModal = false; }
                }
            }).mount('#leads-pro-app');
        </script>
    @endPushOnce
</x-admin::layouts>
