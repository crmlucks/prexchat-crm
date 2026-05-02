import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { 
  Home, 
  MapPin, 
  Maximize, 
  BedDouble, 
  Bath, 
  Car, 
  Tag, 
  Plus,
  Filter,
  DollarSign
} from 'lucide-react';

const Properties = () => {
  const [properties, setProperties] = useState([
    {
      id: 1,
      title: 'Penthouse de Lujo - Miraflores',
      price: '450,000',
      type: 'Departamento',
      status: 'available',
      area: '240',
      rooms: 3,
      baths: 4,
      parking: 2,
      location: 'Lima, Miraflores',
      image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80'
    },
    {
      id: 2,
      title: 'Casa de Campo con Piscina',
      price: '320,000',
      type: 'Casa',
      status: 'reserved',
      area: '1,200',
      rooms: 4,
      baths: 3,
      parking: 4,
      location: 'Cieneguilla, Lima',
      image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800&q=80'
    }
  ]);

  return (
    <div className="flex flex-col gap-8 animate-fade">
      
      {/* ── HEADER ── */}
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-3xl font-extrabold text-white">Inventario de <span className="text-purple-500">Propiedades</span></h2>
          <p className="text-slate-500 text-sm mt-1">Gestiona tus activos y listados disponibles.</p>
        </div>
        <div className="flex gap-4">
          <button className="bg-white/5 border border-white/10 px-4 py-3 rounded-xl text-slate-300 flex items-center gap-2 hover:bg-white/10 transition-all">
            <Filter size={18} /> Filtros
          </button>
          <button className="bg-purple-600 hover:bg-purple-500 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-purple-500/20">
            <Plus size={20} /> Registrar Propiedad
          </button>
        </div>
      </div>

      {/* ── GRID DE PROPIEDADES ── */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {properties.map((prop) => (
          <motion.div 
            key={prop.id}
            whileHover={{ y: -10 }}
            className="bg-[#111827] border border-white/5 rounded-[2.5rem] overflow-hidden group shadow-2xl"
          >
            {/* Image Wrap */}
            <div className="relative h-64 overflow-hidden">
              <img 
                src={prop.image} 
                alt={prop.title} 
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              />
              <div className="absolute top-4 left-4 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white">
                {prop.type}
              </div>
              <div className={`absolute top-4 right-4 px-3 py-1 rounded-full text-[10px] font-bold uppercase ${prop.status === 'available' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-orange-500/20 text-orange-400'}`}>
                {prop.status === 'available' ? 'Disponible' : 'Reservada'}
              </div>
            </div>

            {/* Content */}
            <div className="p-8">
              <div className="flex items-center gap-2 text-slate-500 text-xs mb-3 font-semibold">
                <MapPin size={14} className="text-purple-500" /> {prop.location}
              </div>
              <h3 className="text-xl font-extrabold text-white mb-4 leading-tight">{prop.title}</h3>
              
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="flex items-center gap-2 text-slate-400">
                  <div className="p-2 bg-white/5 rounded-lg"><Maximize size={14} /></div>
                  <span className="text-xs font-bold">{prop.area} m²</span>
                </div>
                <div className="flex items-center gap-2 text-slate-400">
                  <div className="p-2 bg-white/5 rounded-lg"><BedDouble size={14} /></div>
                  <span className="text-xs font-bold">{prop.rooms} Hab.</span>
                </div>
                <div className="flex items-center gap-2 text-slate-400">
                  <div className="p-2 bg-white/5 rounded-lg"><Bath size={14} /></div>
                  <span className="text-xs font-bold">{prop.baths} Baños</span>
                </div>
                <div className="flex items-center gap-2 text-slate-400">
                  <div className="p-2 bg-white/5 rounded-lg"><Car size={14} /></div>
                  <span className="text-xs font-bold">{prop.parking} Coch.</span>
                </div>
              </div>

              <div className="flex items-center justify-between pt-6 border-t border-white/5">
                <div className="text-2xl font-extrabold text-white">
                  <span className="text-purple-500 text-sm font-bold mr-1">$</span>{prop.price}
                </div>
                <button className="bg-white/5 hover:bg-purple-600 p-3 rounded-xl transition-all group-hover:shadow-[0_0_15px_rgba(147,51,234,0.3)]">
                  <ChevronRight size={18} className="text-slate-400 group-hover:text-white" />
                </button>
              </div>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );
};

const ChevronRight = ({ size, className }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}>
    <path d="m9 18 6-6-6-6"/>
  </svg>
);

export default Properties;
