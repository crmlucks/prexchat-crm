import React, { useState } from 'react';
import { 
  LayoutDashboard, 
  Users, 
  MessageSquare, 
  Home, 
  Settings, 
  Search,
  Bell
} from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import LeadsPro from './components/LeadsPro';
import Properties from './components/Properties';
import WhatsAppHub from './components/WhatsAppHub';

function App() {
  const [activeTab, setActiveTab] = useState('dashboard');

  const menuItems = [
    { id: 'dashboard', icon: LayoutDashboard, label: 'Dashboard' },
    { id: 'leads', icon: Users, label: 'Leads Pro' },
    { id: 'whatsapp', icon: MessageSquare, label: 'WhatsApp AI' },
    { id: 'properties', icon: Home, label: 'Propiedades' },
    { id: 'settings', icon: Settings, label: 'Configuración' },
  ];

  return (
    <div className="flex h-screen w-full bg-[#0b0f1a] text-slate-200 overflow-hidden font-['Outfit']">
      
      {/* ── SIDEBAR ── */}
      <aside className="w-64 bg-[#111827] border-r border-white/5 flex flex-col p-6 z-20">
        <div className="flex items-center gap-3 mb-10 px-2">
          <div className="w-8 h-8 bg-purple-600 rounded-lg shadow-[0_0_15px_rgba(147,51,234,0.5)] flex items-center justify-center font-bold text-white">P</div>
          <h1 className="text-xl font-extrabold tracking-tight">Prexup<span className="text-purple-500">AI</span></h1>
        </div>

        <nav className="flex-1 space-y-2">
          {menuItems.map((item) => (
            <button
              key={item.id}
              onClick={() => setActiveTab(item.id)}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 ${
                activeTab === item.id 
                ? 'bg-purple-600/10 text-purple-400 border border-purple-500/20' 
                : 'text-slate-400 hover:bg-white/5 hover:text-slate-200'
              }`}
            >
              <item.icon size={20} />
              <span className="font-semibold text-sm">{item.label}</span>
              {activeTab === item.id && <motion.div layoutId="active" className="ml-auto w-1 h-4 bg-purple-500 rounded-full" />}
            </button>
          ))}
        </nav>

        <div className="mt-auto p-4 bg-white/5 rounded-2xl border border-white/5">
          <p className="text-[10px] text-slate-500 uppercase font-extrabold mb-1">Plan Actual</p>
          <p className="text-xs font-bold text-slate-200">Real Estate Enterprise</p>
        </div>
      </aside>

      {/* ── MAIN CONTENT ── */}
      <main className="flex-1 flex flex-col overflow-hidden relative">
        
        {/* Top Header */}
        <header className="h-20 border-b border-white/5 flex items-center justify-between px-10 bg-[#0b0f1a]/80 backdrop-blur-md z-10">
          <div className="flex items-center gap-4 bg-white/5 px-4 py-2 rounded-xl border border-white/5 w-96">
            <Search size={18} className="text-slate-500" />
            <input type="text" placeholder="Buscar prospectos o casas..." className="bg-transparent border-none outline-none text-sm w-full" />
          </div>
          
          <div className="flex items-center gap-6">
            <button className="relative p-2 text-slate-400 hover:text-white transition-colors">
              <Bell size={20} />
              <span className="absolute top-2 right-2 w-2 h-2 bg-purple-500 rounded-full border-2 border-[#0b0f1a]"></span>
            </button>
            <div className="flex items-center gap-3 pl-6 border-l border-white/10">
              <div className="text-right">
                <p className="text-xs font-bold">Admin Usuario</p>
                <p className="text-[10px] text-purple-400">Verificado</p>
              </div>
              <div className="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl"></div>
            </div>
          </div>
        </header>

        {/* Dynamic Content Rendering */}
        <div className="flex-1 overflow-y-auto p-10 custom-scrollbar">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeTab}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.3 }}
              className="h-full"
            >
              {activeTab === 'dashboard' && (
                <div className="space-y-8">
                  <div className="flex justify-between items-end">
                    <div>
                      <h2 className="text-3xl font-extrabold mb-1 text-white">Resumen Inmobiliario 👋</h2>
                      <p className="text-slate-500">Monitorea el rendimiento de tus ventas e IA.</p>
                    </div>
                  </div>

                  {/* Stats Grid */}
                  <div className="grid grid-cols-4 gap-6">
                    {[
                      { label: 'Leads Nuevos', value: '124', growth: '+12%', color: 'text-blue-400' },
                      { label: 'En Negociación', value: '45', growth: '+5%', color: 'text-purple-400' },
                      { label: 'Ventas del Mes', value: '$2.4M', growth: '+25%', color: 'text-emerald-400' },
                      { label: 'WhatsApp AI Hits', value: '1,204', growth: '+40%', color: 'text-orange-400' },
                    ].map((stat, i) => (
                      <div key={i} className="bg-white/5 border border-white/5 p-6 rounded-[2rem] hover:border-purple-500/30 transition-all">
                        <p className="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">{stat.label}</p>
                        <div className="flex items-end justify-between">
                          <h3 className="text-2xl font-extrabold text-white">{stat.value}</h3>
                          <span className={`text-[10px] font-bold ${stat.color} bg-white/5 px-2 py-1 rounded-md`}>{stat.growth}</span>
                        </div>
                      </div>
                    ))}
                  </div>

                  {/* Property Preview */}
                  <div className="bg-white/5 rounded-[2.5rem] border border-white/5 p-8 mt-10">
                    <h3 className="text-lg font-bold mb-6 text-white">Últimas Propiedades Registradas</h3>
                    <div className="grid grid-cols-2 gap-6">
                      <div className="flex items-center gap-4 bg-[#0b0f1a] p-4 rounded-2xl border border-white/5">
                        <div className="w-20 h-20 bg-slate-800 rounded-xl overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=200&q=80" className="w-full h-full object-cover" />
                        </div>
                        <div>
                          <p className="font-bold text-sm">Penthouse Miraflores</p>
                          <p className="text-xs text-slate-500">$450,000</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              )}

              {activeTab === 'leads' && <LeadsPro />}
              
              {activeTab === 'properties' && <Properties />}

              {activeTab === 'whatsapp' && <WhatsAppHub />}
              
            </motion.div>
          </AnimatePresence>
        </div>
      </main>
    </div>
  );
}

export default App;
