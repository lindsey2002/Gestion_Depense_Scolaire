<template>
  <div class="bg-white p-8 max-w-3xl mx-auto border border-gray-300 rounded-2xl shadow-sm print:border-none print:shadow-none print:p-2" id="section-recu">
    
    <div class="text-center border-b border-gray-200 pb-5 mb-6">
      <h1 class="text-3xl font-black tracking-wider text-gray-900 font-serif">GROUPE ISI</h1>
      <h2 class="text-sm font-bold text-emerald-600 uppercase tracking-widest mt-0.5">Institut Supérieur d'Informatique</h2>
      <p class="text-xs text-gray-500 italic mt-0.5">« Un institut tourné vers les métiers d'avenir »</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-6 items-stretch">
      
      <div class="md:col-span-5 bg-slate-50 p-4 rounded-xl border border-slate-200/60 flex flex-col justify-between">
        <div>
          <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Document Officiel</span>
          <h3 class="text-base font-bold text-gray-900 mt-0.5">Reçu N° : <span class="font-mono text-emerald-600">{{ paiement.numero_recu }}</span></h3>
        </div>
        <div class="mt-4 space-y-1 text-xs text-gray-600">
          <p><span class="text-gray-400">Date d'encaissement :</span> <span class="font-semibold text-gray-800">{{ formatDialogueDate(paiement.date_paiement) }}</span></p>
          <p><span class="text-gray-400">Classe :</span> <span class="font-semibold text-gray-800">{{ eleve.classe?.nom }}</span></p>
          <p><span class="text-gray-400">Vague :</span> <span class="font-semibold text-gray-800">{{ eleve.classe?.vague?.nom }}</span></p>
          <p><span class="text-gray-400">Mode de paiement :</span> <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800 uppercase tracking-wide">{{ paiement.mode_paiement?.replace('_', ' ') }}</span></p>
        </div>
      </div>

      <div class="md:col-span-7 bg-slate-50 p-5 rounded-xl border border-slate-200/60 flex flex-col justify-center items-center text-center">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Étudiant</span>
        <h2 class="text-xl font-black text-gray-900 mt-1 uppercase tracking-tight">
          {{ eleve.prenom }} {{ eleve.nom }}
        </h2>
        <div class="mt-3 px-4 py-1 bg-emerald-50 border border-emerald-200 rounded-full">
          <p class="text-xs text-emerald-700 font-mono font-bold">
            Matricule : {{ eleve.matricule || 'En cours...' }}
          </p>
        </div>
      </div>
    </div>

    <div class="bg-slate-50/50 rounded-xl border border-slate-200/60 p-5 mb-4">
      <div class="flex flex-col sm:flex-row justify-between border-b border-slate-200 pb-3 mb-4 text-xs text-gray-700">
        <p><span class="text-gray-400">Nature du versement :</span> <span class="font-bold uppercase text-emerald-600">{{ paiement.type_paiement === 'inscription' ? "Frais d'inscription" : "Mensualité" }}</span></p>
        <p v-if="paiement.type_paiement === 'mensualite'"><span class="text-gray-400">À titre de :</span> <span class="font-bold text-gray-900 uppercase">{{ paiement.mois }}</span></p>
        <p><span class="text-gray-400">Année Académique :</span> <span class="font-mono font-semibold">{{ paiement.annee_academique || '2025-2026' }}</span></p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
        <div class="p-2.5 bg-white border border-slate-200 rounded-lg">
          <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Frais Scolarité</span>
          <span class="text-sm font-bold font-mono text-gray-800">{{ formatDialogueCurrency(paiement.montant) }}</span>
        </div>
        <div class="p-2.5 bg-white border border-slate-100 rounded-lg opacity-60">
          <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Restauration</span>
          <span class="text-sm font-bold font-mono text-gray-400">0 F</span>
        </div>
        <div class="p-2.5 bg-white border border-slate-100 rounded-lg opacity-60">
          <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Transport</span>
          <span class="text-sm font-bold font-mono text-gray-400">0 F</span>
        </div>
        <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg">
          <span class="text-[10px] text-emerald-700 uppercase tracking-wider block font-bold">Total Perçu</span>
          <span class="text-base font-black font-mono text-emerald-700">{{ formatDialogueCurrency(paiement.montant) }}</span>
        </div>
      </div>
    </div>

    <div class="bg-amber-50 border border-amber-200/70 rounded-xl p-3 flex items-center gap-2 mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <p class="text-[11px] text-amber-800 font-medium">
        <span class="font-bold">NB :</span> Le paiement des frais d'écolage doit s'effectuer au plus tard le <span class="underline font-bold">5 de chaque mois</span>.
      </p>
    </div>

    <div v-if="eleve.echeancier || eleve.classe?.echeancier" class="border-t border-gray-200 pt-4 mb-6">
      <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2.5">Situation de l'échéancier annuel</h4>
      
      <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-10 gap-1.5 text-center">
        <div v-for="echeance in (eleve.echeancier || eleve.classe.echeancier)" :key="echeance.mois" 
             class="p-2 rounded-lg border text-center transition-all"
             :class="Number(echeance.solde) === 0 
                ? 'bg-emerald-50 border-emerald-200 text-emerald-700' 
                : 'bg-white border-slate-200 text-slate-700'">
          <span class="text-[10px] font-bold uppercase tracking-tight block">{{ echeance.mois }}</span>
          <span class="text-xs font-mono font-bold mt-0.5 block">
            {{ Number(echeance.solde) === 0 ? '0 F' : `${formatDialogueCurrency(echeance.solde)}` }}
          </span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 text-[10px] text-gray-400 pt-4 border-t border-dashed border-gray-200">
      <div class="text-left">
        <p class="font-medium text-gray-500">Signature Étudiant/Parent</p>
        <div class="h-14 border-b border-dashed border-slate-200 mt-1"></div>
        <p class="text-[9px] text-gray-400 mt-1 italic">Lu et approuvé</p>
      </div>
      <div class="text-right">
        <p class="font-medium text-gray-500">Signature Caisse (Compta)</p>
        <div class="h-14 border-b border-dashed border-slate-200 mt-1"></div>
        <p class="text-[9px] text-gray-400 mt-1 italic">Fait à Dakar, le {{ formatDialogueDate(paiement.date_paiement).split(' à ')[0] }}</p>
      </div>
    </div>

    <div class="mt-6 text-center print:hidden">
      <button  
        @click="imprimerLeRecu"
        class="w-full bg-slate-900 text-white py-2.5 px-4 rounded-xl text-sm font-semibold hover:bg-slate-800 transition shadow-sm flex items-center justify-center space-x-2 transform hover:-translate-y-0.5 duration-150"
      >
        <span>🖨️ Imprimer le reçu officiel</span>
      </button>
    </div>

  </div>
</template>

<script setup>
defineProps({
  paiement: { type: Object, required: true },
  eleve: { type: Object, required: true }
});

const formatDialogueCurrency = (valeur) => {
  if (!valeur && valeur !== 0) return '0 F';
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(valeur);
};

const formatDialogueDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
  });
};

const couperDatePourLieu = (dateString) => {
  return dateString ? dateString.split(' à ')[0] : '';
};

const ImprimerLeRecu = () => {
  window.print();
};
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #section-recu, #section-recu * {
    visibility: visible;
  }
  #section-recu {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none;
    box-shadow: none;
    padding: 0;
  }
  /* Force l'affichage des couleurs de fonds (badges verts et gris) à l'impression papier */
  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
}
</style>