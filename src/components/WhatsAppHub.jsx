import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Send, 
  User, 
  Sparkles, 
  DollarSign, 
  MapPin, 
  Home, 
  TrendingUp,
  MoreVertical,
  CheckCheck
} from 'lucide-react';

const WhatsAppHub = () => {
  const [selectedChat, setSelectedChat] = useState(1);
  const [message, setMessage] = useState('');

  const chats = [
    { id: 1, name: 'Roberto Carlos', lastMsg: 'Me interesa el departamento de Miraflores', time: '12:45', status: 'hot', score: 85 },
    { id: 2, name: 'Maria Jose', lastMsg: '¿Cuál es el precio final?', time: '11:20', status: 'warm', score: 62 },
    { id: 3, name: 'Andrés Bello', lastMsg: 'Gracias por la información', time: 'Ayer', status: 'cold', score: 24 },
  ];

  const leadProfile = {
    budget: '$450,000',
    location: 'Miraflores, San Isidro',
    propertyType: 'Penthouse / Departamento',
    urgency: 'Alta (Compra en 30 días)',
    summary: 'Cliente con financiamiento aprobado buscando vivienda familiar con terraza amplia.'
  };

  return (
    <div className="flex h-full gap-6 animate-fade">
      
      {/* ── LISTA DE CHATS ── */}
      <aside className="w-80 bg-[#111827] border border-white/5 rounded-[2rem] flex flex-col overflow-hidden">
        <div className="p-6 border-b border-white/5 bg-white/[0.02]">
          <h3 className="font-extrabold text-lg">Conversaciones</h3>
        </div>
        <div className="flex-1 overflow-y-auto custom-scrollbar p-2">
          {chats.map(chat => (
            <div 
              key={chat.id}
              onClick={() => setSelectedChat(chat.id)}
              className={`p-4 rounded-2xl cursor-pointer transition-all mb-1 ${selectedChat === chat.id ? 'bg-purple-600/10 border border-purple-500/20' : 'hover:bg-white/5 border border-transparent'}`}
            >
              <div className="flex justify-between items-start mb-1">
                <span className="font-bold text-sm text-slate-100">{chat.name}</span>
                <span className="text-[10px] text-slate-500">{chat.time}</span>
              </div>
              <p className="text-xs text-slate-500 truncate">{chat.lastMsg}</p>
              <div className="flex items-center gap-2 mt-3">
                <div className={`w-2 h-2 rounded-full ${chat.status === 'hot' ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-orange-400'}`}></div>
                <span className="text-[9px] font-bold uppercase tracking-widest text-slate-400">Score IA: {chat.score}%</span>
              </div>
            </div>
          ))}
        </div>
      </aside>

      {/* ── VENTANA DE CHAT ── */}
      <main className="flex-1 bg-[#111827] border border-white/5 rounded-[2rem] flex flex-col overflow-hidden shadow-2xl relative">
        {/* Header */}
        <header className="p-6 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center font-bold text-lg">R</div>
            <div>
              <h3 className="font-extrabold">Roberto Carlos</h3>
              <p className="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">En Línea • IA Activa</p>
            </div>
          </div>
          <button className="text-slate-500 hover:text-white p-2 transition-colors"><MoreVertical size={20} /></button>
        </header>

        {/* Messages */}
        <div className="flex-1 overflow-y-auto p-8 space-y-6 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-80">
          <div className="flex flex-col items-start gap-1">
            <div className="bg-white/5 p-4 rounded-2xl rounded-bl-none max-w-md text-sm border border-white/5">
              Hola, vi el Penthouse en Miraflores. ¿Sigue disponible?
            </div>
            <span className="text-[9px] text-slate-600 ml-1">12:44 PM</span>
          </div>
          
          <div className="flex flex-col items-end gap-1">
            <div className="bg-purple-600 p-4 rounded-2xl rounded-br-none max-w-md text-sm shadow-lg shadow-purple-500/20">
              ¡Hola Roberto! Sí, el Penthouse de Miraflores está disponible. Cuenta con 240m² y vista panorámica al mar. ¿Te gustaría agendar una visita mañana?
            </div>
            <div className="flex items-center gap-1 text-[9px] text-slate-600 mr-1">
              12:45 PM <CheckCheck size={12} className="text-purple-500" />
            </div>
          </div>
        </div>

        {/* Input */}
        <footer className="p-6 bg-white/[0.02] border-t border-white/5">
          <div className="flex items-center gap-4 bg-[#0b0f1a] p-2 pl-6 rounded-2xl border border-white/10">
            <input 
              type="text" 
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              placeholder="Escribe un mensaje manual..." 
              className="flex-1 bg-transparent border-none outline-none text-sm"
            />
            <button className="bg-purple-600 p-3 rounded-xl text-white hover:bg-purple-500 transition-all shadow-lg shadow-purple-500/20">
              <Send size={18} />
            </button>
          </div>
        </footer>
      </main>

      {/* ── EXPEDIENTE IA (DERECHA) ── */}
      <aside className="w-80 flex flex-col gap-6">
        <div className="bg-[#111827] border border-white/5 rounded-[2rem] p-8 flex flex-col gap-6 shadow-2xl">
          <div className="flex items-center gap-2 text-purple-400 font-extrabold text-[10px] uppercase tracking-widest">
            <Sparkles size={14} /> Perfil Cognitivo IA
          </div>
          
          <div className="space-y-6">
            <div className="flex items-start gap-4">
              <div className="p-3 bg-white/5 rounded-xl text-fuchsia-500"><DollarSign size={18} /></div>
              <div>
                <p className="text-[10px] font-extrabold text-slate-500 uppercase">Presupuesto</p>
                <p className="font-bold text-slate-200">{leadProfile.budget}</p>
              </div>
            </div>
            <div className="flex items-start gap-4">
              <div className="p-3 bg-white/5 rounded-xl text-blue-500"><MapPin size={18} /></div>
              <div>
                <p className="text-[10px] font-extrabold text-slate-500 uppercase">Zonas de Interés</p>
                <p className="font-bold text-slate-200">{leadProfile.location}</p>
              </div>
            </div>
            <div className="flex items-start gap-4">
              <div className="p-3 bg-white/5 rounded-xl text-orange-500"><Home size={18} /></div>
              <div>
                <p className="text-[10px] font-extrabold text-slate-500 uppercase">Tipo de Interés</p>
                <p className="font-bold text-slate-200">{leadProfile.propertyType}</p>
              </div>
            </div>
          </div>

          <div className="bg-purple-600/5 border border-purple-500/20 p-5 rounded-2xl">
            <p className="text-[10px] font-extrabold text-purple-400 uppercase mb-2">Resumen AI</p>
            <p className="text-xs leading-relaxed text-slate-400">{leadProfile.summary}</p>
          </div>

          <button className="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-2xl font-extrabold text-sm transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 mt-4">
            <TrendingUp size={18} /> Convertir en Oportunidad
          </button>
        </div>
      </aside>

    </div>
  );
};

export default WhatsAppHub;
