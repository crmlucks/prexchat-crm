import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Plus, 
  LayoutGrid, 
  List as ListIcon, 
  MoreHorizontal, 
  Phone, 
  Mail, 
  DollarSign,
  Tag,
  X,
  CheckCircle2
} from 'lucide-react';

const LeadsPro = () => {
  const [view, setView] = useState('kanban'); // 'kanban' or 'list'
  const [showModal, setShowModal] = useState(false);
  const [leads, setLeads] = useState([
    { id: 1, name: 'Roberto Carlos', budget: '250,000', source: 'WhatsApp AI', stage: 'nuevo', phone: '+51 987 654 321', intent: 'hot' },
    { id: 2, name: 'Maria Jose', budget: '180,000', source: 'Facebook Ads', stage: 'cualificado', phone: '+51 912 345 678', intent: 'warm' },
    { id: 3, name: 'Andrés Bello', budget: '450,000', source: 'Web', stage: 'cita', phone: '+51 933 221 100', intent: 'ready' },
  ]);

  const stages = [
    { id: 'nuevo', title: 'Nuevo Prospecto', color: '#6366f1' },
    { id: 'cualificado', title: 'Cualificado IA', color: '#9333ea' },
    { id: 'cita', title: 'Cita Programada', color: '#d946ef' },
    { id: 'cierre', title: 'Cierre Inminente', color: '#10b981' },
  ];

  return (
    <div className="flex flex-col h-full gap-8 animate-fade">
      
      {/* ── HEADER DE LEADS ── */}
      <div className="flex justify-between items-center">
        <div className="flex items-center gap-6">
          <h2 className="text-3xl font-extrabold">Gestión de <span className="text-purple-500">Leads</span></h2>
          <div className="flex bg-white/5 p-1 rounded-xl border border-white/5">
            <button 
              onClick={() => setView('kanban')}
              className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${view === 'kanban' ? 'bg-[#0b0f1a] text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'}`}
            >
              <LayoutGrid size={16} /> Pipeline
            </button>
            <button 
              onClick={() => setView('list')}
              className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all ${view === 'list' ? 'bg-[#0b0f1a] text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'}`}
            >
              <ListIcon size={16} /> Lista
            </button>
          </div>
        </div>

        <button 
          onClick={() => setShowModal(true)}
          className="bg-gradient-to-r from-fuchsia-600 to-purple-600 hover:from-fuchsia-500 hover:to-purple-500 text-white px-6 py-3 rounded-xl font-extrabold flex items-center gap-2 shadow-xl shadow-purple-500/20 transition-transform active:scale-95"
        >
          <Plus size={20} /> Nuevo Prospecto
        </button>
      </div>

      {/* ── VISTA CONTENIDO ── */}
      <div className="flex-1 overflow-hidden">
        <AnimatePresence mode="wait">
          {view === 'kanban' ? (
            <motion.div 
              key="kanban"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              className="flex gap-6 h-full overflow-x-auto pb-4 custom-scrollbar"
            >
              {stages.map(stage => (
                <div key={stage.id} className="min-width-[320px] w-80 flex flex-col gap-4">
                  <div className="flex justify-between items-center px-2">
                    <div className="flex items-center gap-2">
                      <div className="w-2 h-2 rounded-full" style={{ backgroundColor: stage.color }}></div>
                      <h3 className="font-bold text-sm uppercase tracking-widest text-slate-400">{stage.title}</h3>
                    </div>
                    <span className="bg-white/5 px-2 py-0.5 rounded text-[10px] font-bold">{leads.filter(l => l.stage === stage.id).length}</span>
                  </div>

                  <div className="flex-1 bg-white/[0.02] border border-white/5 rounded-3xl p-4 space-y-4 overflow-y-auto custom-scrollbar">
                    {leads.filter(l => l.stage === stage.id).map(lead => (
                      <motion.div 
                        key={lead.id}
                        whileHover={{ y: -5, borderColor: 'rgba(147, 51, 234, 0.4)' }}
                        className="bg-[#111827] border border-white/5 p-5 rounded-2xl cursor-pointer transition-colors shadow-xl"
                      >
                        <div className="flex justify-between items-start mb-4">
                          <span className="text-[9px] font-extrabold text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded uppercase tracking-tighter">{lead.source}</span>
                          <button className="text-slate-600 hover:text-white transition-colors"><MoreHorizontal size={16} /></button>
                        </div>
                        <h4 className="font-bold text-slate-100 mb-1">{lead.name}</h4>
                        <p className="text-xs text-slate-500 mb-4">{lead.phone}</p>
                        
                        <div className="flex items-center justify-between mt-auto pt-4 border-t border-white/5">
                          <div className="flex items-center gap-1 text-slate-300 font-extrabold text-sm">
                            <span className="text-slate-500">$</span>{lead.budget}
                          </div>
                          <div className={`w-2 h-2 rounded-full ${lead.intent === 'hot' ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-orange-500'}`}></div>
                        </div>
                      </motion.div>
                    ))}
                  </div>
                </div>
              ))}
            </motion.div>
          ) : (
            <motion.div 
              key="list"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              className="bg-white/[0.02] border border-white/5 rounded-3xl overflow-hidden shadow-2xl"
            >
              <table className="w-full border-collapse">
                <thead>
                  <tr className="border-b border-white/5 bg-white/5">
                    <th className="text-left p-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Prospecto</th>
                    <th className="text-left p-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Presupuesto</th>
                    <th className="text-left p-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Canal</th>
                    <th className="text-left p-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Estado</th>
                    <th className="p-5"></th>
                  </tr>
                </thead>
                <tbody>
                  {leads.map(lead => (
                    <tr key={lead.id} className="border-b border-white/5 hover:bg-white/[0.02] transition-colors group">
                      <td className="p-5">
                        <div className="flex flex-col">
                          <span className="font-bold text-slate-200">{lead.name}</span>
                          <span className="text-xs text-slate-500">{lead.phone}</span>
                        </div>
                      </td>
                      <td className="p-5"><span className="text-emerald-400 font-extrabold">$ {lead.budget}</span></td>
                      <td className="p-5"><span className="text-xs bg-white/5 px-2 py-1 rounded-md text-slate-400">{lead.source}</span></td>
                      <td className="p-5">
                        <span className="text-[10px] font-bold uppercase border border-white/10 px-3 py-1 rounded-full text-slate-400">
                          {lead.stage}
                        </span>
                      </td>
                      <td className="p-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                        <button className="p-2 hover:bg-purple-600 rounded-lg transition-colors"><ChevronRight size={16} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      {/* ── MODAL: NUEVO PROSPECTO ── */}
      <AnimatePresence>
        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-6">
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setShowModal(false)}
              className="absolute inset-0 bg-black/80 backdrop-blur-md"
            />
            
            <motion.div 
              initial={{ opacity: 0, scale: 0.9, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.9, y: 20 }}
              className="relative w-full max-w-2xl bg-[#111827] border border-white/10 rounded-[2rem] overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] flex flex-col max-h-[90vh]"
            >
              {/* Modal Header */}
              <div className="p-8 border-b border-white/5 flex justify-between items-center">
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 bg-fuchsia-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-fuchsia-500/20">+</div>
                  <div>
                    <h2 className="text-xl font-extrabold">Nuevo prospecto</h2>
                    <p className="text-xs text-slate-500 font-semibold uppercase tracking-wider">Gestión Inmobiliaria Pro</p>
                  </div>
                </div>
                <button onClick={() => setShowModal(false)} className="text-slate-500 hover:text-white transition-colors p-2"><X size={24} /></button>
              </div>

              {/* Modal Body */}
              <div className="p-8 overflow-y-auto custom-scrollbar space-y-8">
                {/* Section 1 */}
                <div className="bg-white/5 p-6 rounded-3xl border border-white/5">
                  <div className="flex items-center gap-2 text-fuchsia-400 font-extrabold text-[10px] uppercase tracking-widest mb-6">
                    <Users size={14} /> Datos del Prospecto
                  </div>
                  <div className="grid grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label className="text-[10px] font-extrabold text-slate-500 uppercase">Nombre Completo *</label>
                      <input type="text" className="w-full bg-[#0b0f1a] border border-white/10 rounded-xl p-3 outline-none focus:border-fuchsia-500 transition-colors" placeholder="Nombre del cliente" />
                    </div>
                    <div className="space-y-2">
                      <label className="text-[10px] font-extrabold text-slate-500 uppercase">Teléfono *</label>
                      <div className="relative">
                        <Phone size={14} className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600" />
                        <input type="text" className="w-full bg-[#0b0f1a] border border-white/10 rounded-xl p-3 pl-10 outline-none focus:border-fuchsia-500 transition-colors" placeholder="+51..." />
                      </div>
                    </div>
                  </div>
                </div>

                {/* Section 2 */}
                <div className="bg-white/5 p-6 rounded-3xl border border-white/5 relative overflow-hidden group">
                   <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <DollarSign size={80} className="text-fuchsia-500" />
                   </div>
                  <div className="flex items-center gap-2 text-fuchsia-400 font-extrabold text-[10px] uppercase tracking-widest mb-6 relative z-10">
                    <DollarSign size={14} /> Perfil Económico
                  </div>
                  <div className="grid grid-cols-2 gap-6 relative z-10">
                    <div className="space-y-2">
                      <label className="text-[10px] font-extrabold text-slate-500 uppercase">Presupuesto Estimado</label>
                      <div className="relative">
                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-fuchsia-500 font-bold">$</span>
                        <input type="text" className="w-full bg-[#0b0f1a] border border-white/10 rounded-xl p-3 pl-10 outline-none focus:border-fuchsia-500 transition-colors" placeholder="0.00" />
                      </div>
                    </div>
                    <div className="space-y-2">
                      <label className="text-[10px] font-extrabold text-slate-500 uppercase">Proyecto Interés</label>
                      <select className="w-full bg-[#0b0f1a] border border-white/10 rounded-xl p-3 outline-none focus:border-fuchsia-500 transition-colors appearance-none">
                        <option>Residencial Primavera</option>
                        <option>Torre Ejecutiva</option>
                        <option>Hacienda Real</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              {/* Modal Footer */}
              <div className="p-8 border-t border-white/5 flex justify-end gap-4 bg-white/[0.02]">
                <button onClick={() => setShowModal(false)} className="text-slate-500 font-bold hover:text-white px-6 transition-colors">Cancelar</button>
                <button className="bg-fuchsia-600 hover:bg-fuchsia-500 text-white px-8 py-4 rounded-2xl font-extrabold shadow-lg shadow-fuchsia-500/20 flex items-center gap-2 transition-transform active:scale-95">
                  <CheckCircle2 size={18} /> Crear Prospecto
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

    </div>
  );
};

const ChevronRight = ({ size, className }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}>
    <path d="m9 18 6-6-6-6"/>
  </svg>
);

export default LeadsPro;
