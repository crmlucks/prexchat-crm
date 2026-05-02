<x-admin::layouts>
    <x-slot:title>
        Gestión de Leads Pro
    </x-slot>

    @push('styles')
    <style>
        /* ── CORRECCIÓN DE PRIORIDAD Y ESTILOS ── */
        .leads-pro-container {
            font-family: 'Outfit', sans-serif !important;
            background: #0b0f1a !important;
            color: #f8fafc !important;
            min-height: calc(100vh - 60px);
            padding: 24px;
            position: relative;
            z-index: 1;
            margin: -20px; /* Compensa el padding del layout de Krayin */
        }

        .leads-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-left h1 { font-size: 28px; font-weight: 800; margin: 0 0 15px; color: white !important; }
        .header-left h1 span { color: #d946ef !important; }
        
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
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .view-switcher button.active { background: #0b0f1a; color: white; }

        .btn-new-lead {
            background: linear-gradient(135deg, #d946ef, #9333ea);
            color: white !important;
            border: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        /* KANBAN */
        .kanban-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 10px;
            height: calc(100vh - 250px);
        }

        .kanban-column {
            min-width: 300px;
            background: rgba(31, 41, 55, 0.5);
            border-radius: 20px;
            padding: 15px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .column-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .column-header h3 { font-size: 14px; font-weight: 800; color: #e5e7eb; margin: 0; }

        .lead-card {
            background: #1f2937;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .lead-card h4 { margin: 0 0 10px; font-size: 14px; color: white; }

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
        }

        .form-section { background: #1f2937; padding: 20px; border-radius: 16px; margin-bottom: 20px; }
        .section-title { font-size: 11px; font-weight: 800; color: #d946ef; margin-bottom: 15px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group label { display: block; font-size: 10px; color: #94a3b8; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; background: #0b0f1a; border: 1px solid #374151;
            padding: 10px; border-radius: 8px; color: white;
        }

        .btn-submit { background: #d946ef; color: white; padding: 12px 25px; border-radius: 10px; border: none; font-weight: 800; cursor: pointer; float: right; }
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
                        <p class="budget">💰 @{{ lead.budget }}</p>
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
                            <th style="padding:15px;">Prospecto</th>
                            <th style="padding:15px;">Presupuesto</th>
                            <th style="padding:15px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lead in allLeads" :key="lead.id" style="border-top:1px solid #374151;">
                            <td style="padding:15px;">@{{ lead.name }}</td>
                            <td style="padding:15px; color:#10b981;">@{{ lead.budget }}</td>
                            <td style="padding:15px;">@{{ lead.stage }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── MODAL ── --}}
        <transition name="fade">
            <div v-if="showModal" class="modal-overlay" @@click.self="closeModal">
                <div class="modal-content-glass">
                    <div style="display:flex; justify-content:space-between; margin-bottom:25px;">
                        <h2>Nuevo prospecto</h2>
                        <button @@click="closeModal" style="background:none; border:none; color:white; font-size:24px; cursor:pointer;">×</button>
                    </div>

                    <div class="form-section">
                        <div class="section-title">DATOS DEL PROSPECTO</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>NOMBRE COMPLETO *</label>
                                <input type="text" placeholder="Nombre del cliente">
                            </div>
                            <div class="form-group">
                                <label>TELÉFONO *</label>
                                <input type="text" placeholder="+51...">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">PERFIL ECONÓMICO</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>PRESUPUESTO</label>
                                <input type="text" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label>MONEDA</label>
                                <select><option>USD</option></select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:15px;">
                            <label>DETALLES DE INTERÉS</label>
                            <textarea rows="3"></textarea>
                        </div>
                    </div>

                    <button class="btn-submit">Crear</button>
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
                        board: [
                            { id: 1, title: 'Nuevo prospecto', leads: [{ id: 1, name: 'Juan Perez', budget: '150k' }] },
                            { id: 2, title: 'Cualificado IA', leads: [{ id: 2, name: 'Maria Garcia', budget: '220k' }] },
                            { id: 3, title: 'Cita', leads: [] },
                            { id: 4, title: 'Cierre', leads: [] }
                        ],
                        allLeads: [
                            { id: 1, name: 'Juan Perez', budget: '$150,000', stage: 'Nuevo' }
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
