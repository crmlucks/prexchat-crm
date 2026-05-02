<x-admin::layouts>
    <x-slot:title>
        Gestión de Leads Pro
    </x-slot>

    @push('styles')
    <style>
        /* ── ESTILOS PREMIUM LEADS PRO ── */
        .leads-pro-container {
            font-family: 'Outfit', sans-serif !important;
            background: #0b0f1a !important;
            color: #f8fafc !important;
            min-height: calc(100vh - 60px);
            padding: 24px;
            margin: -20px;
        }

        .leads-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-left h1 { font-size: 28px; font-weight: 800; color: white !important; }
        .header-left h1 span { color: #d946ef !important; }
        
        .view-switcher { display: flex; background: #1f2937; padding: 4px; border-radius: 12px; gap: 4px; }
        .view-switcher button { background: transparent; border: none; color: #94a3b8; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .view-switcher button.active { background: #0b0f1a; color: white; }

        .btn-new-lead {
            background: linear-gradient(135deg, #d946ef, #9333ea);
            color: white !important;
            border: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);
            transition: transform 0.2s;
        }
        .btn-new-lead:hover { transform: scale(1.05); }

        /* KANBAN */
        .kanban-board { display: flex; gap: 20px; overflow-x: auto; padding: 10px; height: calc(100vh - 250px); }
        .kanban-column { min-width: 300px; background: rgba(31, 41, 55, 0.5); border-radius: 20px; padding: 15px; border: 1px solid rgba(255,255,255,0.05); }
        .column-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .column-header h3 { font-size: 14px; font-weight: 800; color: #e5e7eb; }

        .lead-card {
            background: #1f2937;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        .lead-card h4 { margin: 0 0 10px; font-size: 14px; color: white; }
        .card-footer { display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; color: #10b981; font-weight: 800; }

        /* MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); backdrop-filter: blur(10px);
            z-index: 9999; display: flex; align-items: center; justify-content: center;
        }

        .modal-content-glass {
            width: 650px; max-height: 90vh; background: #111827;
            border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);
            padding: 30px; overflow-y: auto; color: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .form-section { background: #1f2937; padding: 20px; border-radius: 16px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.03); }
        .section-title { font-size: 11px; font-weight: 800; color: #d946ef; margin-bottom: 15px; letter-spacing: 1px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group label { display: block; font-size: 10px; color: #94a3b8; margin-bottom: 6px; font-weight: 800; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: #0b0f1a; border: 1px solid #374151;
            padding: 12px; border-radius: 10px; color: white; outline: none; transition: border 0.3s;
        }
        .form-group input:focus { border-color: #d946ef; }

        .btn-submit { background: #d946ef; color: white; padding: 14px 30px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; float: right; box-shadow: 0 4px 15px rgba(217, 70, 239, 0.4); }
        .btn-cancel { background: transparent; border: none; color: #94a3b8; font-weight: 700; cursor: pointer; padding: 14px; }
    </style>
    @endpush

    <div id="leads-pro-app" class="leads-pro-container">
        {{-- ── HEADER ── --}}
        <header class="leads-header">
            <div class="header-left">
                <h1>Leads <span>Pro</span></h1>
                <div class="view-switcher">
                    <button :class="{active: view === 'kanban'}" @@click="view = 'kanban'">Pipeline</button>
                    <button :class="{active: view === 'list'}" @@click="view = 'list'">Lista</button>
                </div>
            </div>
            <div class="header-right">
                <button class="btn-new-lead" @@click="openModal">
                    + Nuevo Prospecto
                </button>
            </div>
        </header>

        {{-- ── KANBAN VIEW ── --}}
        <div v-if="view === 'kanban'" class="kanban-board">
            <div v-for="column in board" :key="column.id" class="kanban-column">
                <div class="column-header">
                    <h3>@{{ column.title }}</h3>
                    <span class="count">@{{ column.leads.length }}</span>
                </div>
                
                <div class="column-body">
                    <div v-for="lead in column.leads" :key="lead.id" class="lead-card">
                        <h4>@{{ lead.name }}</h4>
                        <div class="card-footer">
                            <span>💰 @{{ lead.budget }}</span>
                            <span>@{{ lead.source }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── LIST VIEW ── --}}
        <div v-if="view === 'list'" class="list-view">
            <div class="table-glass">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align:left; color:#6b7280; font-size:12px;">
                            <th style="padding:20px;">Prospecto</th>
                            <th style="padding:20px;">Presupuesto</th>
                            <th style="padding:20px;">Canal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lead in allLeads" :key="lead.id" style="border-top:1px solid #1f2937;">
                            <td style="padding:20px; font-weight:700;">@{{ lead.name }}</td>
                            <td style="padding:20px; color:#10b981; font-weight:800;">@{{ lead.budget }}</td>
                            <td style="padding:20px;">@{{ lead.source }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── MODAL ── --}}
        <transition name="fade">
            <div v-if="showModal" class="modal-overlay" @@click.self="closeModal">
                <div class="modal-content-glass">
                    <div style="display:flex; justify-content:space-between; margin-bottom:25px; align-items:center;">
                        <h2 style="font-size:24px; font-weight:800;">Nuevo prospecto</h2>
                        <button @@click="closeModal" style="background:none; border:none; color:white; font-size:32px; cursor:pointer; opacity:0.5;">×</button>
                    </div>

                    <div class="form-section">
                        <div class="section-title">DATOS DEL PROSPECTO</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>NOMBRE COMPLETO *</label>
                                <input type="text" v-model="form.name" placeholder="Nombre del cliente">
                            </div>
                            <div class="form-group">
                                <label>TELÉFONO *</label>
                                <input type="text" v-model="form.phone" placeholder="+51...">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">PERFIL ECONÓMICO</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>PRESUPUESTO</label>
                                <input type="text" v-model="form.budget" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>CANAL ORIGEN</label>
                                <select v-model="form.source">
                                    <option>WhatsApp AI</option>
                                    <option>Facebook Ads</option>
                                    <option>Instagram</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px;">
                            <label>DETALLES DE INTERÉS</label>
                            <textarea v-model="form.details" rows="3" placeholder="Intereses del cliente..."></textarea>
                        </div>
                    </div>

                    <div style="overflow:hidden; margin-top:20px;">
                        <button class="btn-submit" @@click="createLead">Crear Prospecto</button>
                        <button class="btn-cancel" @@click="closeModal">Cancelar</button>
                    </div>
                </div>
            </div>
        </transition>
    </div>

    @pushOnce('scripts')
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script>
            const { createApp } = Vue;
            createApp({
                data() {
                    return {
                        view: 'kanban',
                        showModal: false,
                        form: {
                            name: '',
                            phone: '',
                            budget: '',
                            source: 'WhatsApp AI',
                            details: ''
                        },
                        board: [
                            { id: 1, title: 'Nuevo prospecto', leads: [{ id: 1, name: 'Juan Perez', budget: '$150,000', source: 'WhatsApp AI' }] },
                            { id: 2, title: 'Cualificado IA', leads: [{ id: 2, name: 'Maria Garcia', budget: '$220,000', source: 'Facebook Ads' }] },
                            { id: 3, title: 'Cita', leads: [] },
                            { id: 4, title: 'Cierre', leads: [] }
                        ]
                    }
                },
                computed: {
                    allLeads() {
                        return this.board.flatMap(col => col.leads);
                    }
                },
                methods: {
                    openModal() { this.showModal = true; },
                    closeModal() { this.showModal = false; },
                    createLead() {
                        if (!this.form.name) {
                            alert('Por favor ingresa el nombre.');
                            return;
                        }

                        // Simulamos la creación añadiéndolo a la primera columna
                        const newLead = {
                            id: Date.now(),
                            name: this.form.name,
                            budget: '$' + (this.form.budget || '0'),
                            source: this.form.source
                        };

                        this.board[0].leads.unshift(newLead);
                        
                        // Limpiamos y cerramos
                        this.form = { name: '', phone: '', budget: '', source: 'WhatsApp AI', details: '' };
                        this.showModal = false;
                        
                        console.log('Lead creado en el frontend');
                    }
                }
            }).mount('#leads-pro-app');
        </script>
    @endPushOnce
</x-admin::layouts>
