function playChime() {
    try {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;
        const ctx = new AudioContextClass();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5

        gain.gain.setValueAtTime(0.18, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start();
        osc.stop(ctx.currentTime + 0.35);
    } catch {
        // Audio might be ignored if user has not interacted with document yet
    }
}

function showToastNotification(data) {
    let container = document.getElementById('finora-realtime-toasts');
    if (!container) {
        container = document.createElement('div');
        container.id = 'finora-realtime-toasts';
        container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto bg-white dark:bg-slate-900 border-2 border-[#00c1de] shadow-2xl rounded-2xl p-4 flex items-start gap-3 transform translate-y-4 opacity-0 transition-all duration-300';
    toast.innerHTML = `
        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-[#00c1de] flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-2xl">shopping_bag</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-xs font-black uppercase tracking-wider text-[#00c1de]">¡Nuevo Pedido Recibido!</span>
                <span class="text-[10px] text-slate-400 font-semibold">${data.fecha || 'Ahora'}</span>
            </div>
            <p class="font-bold text-sm text-slate-900 dark:text-slate-100 truncate mt-0.5">
                #${data.id} • ${data.cliente_nombre}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Total estimado: <span class="font-bold text-slate-800 dark:text-slate-200">Bs ${data.total}</span> (${data.items_count} ${data.items_count === 1 ? 'ítem' : 'ítems'})
            </p>
            <div class="mt-2.5 flex items-center gap-2">
                <a href="${data.url_show}" class="inline-flex items-center gap-1 px-3 py-1 bg-[#00c1de] hover:bg-[#00a3bd] text-white text-xs font-bold rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    Ver pedido
                </a>
                <button type="button" class="close-toast-btn px-2.5 py-1 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    const closeToast = () => {
        toast.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector('.close-toast-btn')?.addEventListener('click', closeToast);

    // Auto dismiss after 12 seconds
    setTimeout(closeToast, 12000);
}

function insertOrderIntoTable(data) {
    const tableBody = document.getElementById('pedidosTableBody');
    if (!tableBody) return;

    // Check if this row is already rendered to avoid duplicate additions
    if (document.getElementById(`pedido-row-${data.id}`)) {
        return;
    }

    // Remove empty state row if present
    const emptyRow = tableBody.querySelector('td[colspan]');
    if (emptyRow) {
        emptyRow.closest('tr')?.remove();
    }

    const row = document.createElement('tr');
    row.id = `pedido-row-${data.id}`;
    row.className = 'hover:bg-slate-50/80 transition-colors bg-cyan-50/60 dark:bg-cyan-950/40 border-l-4 border-l-[#00c1de] animate-pulse';
    row.innerHTML = `
        <td class="py-3 px-4 font-bold text-sm text-finora-navy">
            <a href="${data.url_show}" class="hover:text-finora-blue inline-flex items-center gap-1.5">
                #${data.id}
                <span class="inline-block w-2 h-2 rounded-full bg-[#00c1de]" title="Nuevo pedido recibido"></span>
            </a>
        </td>
        <td class="py-3 px-4 font-bold">
            ${data.cliente_nombre}
        </td>
        <td class="py-3 px-4 text-slate-600">
            ${data.fecha}
        </td>
        <td class="py-3 px-4 text-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border bg-slate-100 border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100">
                ${data.estado}
            </span>
        </td>
        <td class="py-3 px-4 text-center">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 border border-cyan-200 text-cyan-800 dark:bg-cyan-950/70 dark:border-cyan-700 dark:text-cyan-200">
                <span class="material-symbols-outlined text-xs">new_releases</span> Nuevo (${data.items_count} items)
            </span>
        </td>
        <td class="py-3 px-4 text-right font-bold text-finora-navy">
            Bs ${data.total}
        </td>
        <td class="py-3 px-4 text-right">
            <a href="${data.url_show}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-[#00c1de] hover:text-white rounded-xl transition-all shadow-xs" title="Ver y gestionar pedido">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>Ver pedido</span>
            </a>
        </td>
    `;

    tableBody.prepend(row);

    // Stop pulsing after 6 seconds
    setTimeout(() => {
        row.classList.remove('animate-pulse');
    }, 6000);
}

document.addEventListener('DOMContentLoaded', () => {
    // Only run if the user is in the panel
    if (!window.location.pathname.startsWith('/panel')) {
        return;
    }

    let ultimoId = null;
    let isFetching = false;

    // Check for existing highest order id in table if present
    const tableBody = document.getElementById('pedidosTableBody');
    if (tableBody) {
        const firstLink = tableBody.querySelector('tr td a');
        if (firstLink && firstLink.textContent) {
            const match = firstLink.textContent.match(/#(\d+)/);
            if (match) {
                ultimoId = parseInt(match[1], 10);
            }
        }
    }

    async function checkNuevosPedidos() {
        if (isFetching) return;
        isFetching = true;

        try {
            const url = ultimoId !== null
                ? `/panel/pedidos/ultimos?desde_id=${ultimoId}`
                : '/panel/pedidos/ultimos';

            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                const data = await res.json();

                if (ultimoId === null) {
                    // First sync: set benchmark id
                    ultimoId = data.ultimo_id ?? 0;
                } else if (Array.isArray(data.pedidos) && data.pedidos.length > 0) {
                    ultimoId = data.ultimo_id;

                    playChime();
                    data.pedidos.forEach(pedido => {
                        showToastNotification(pedido);
                        insertOrderIntoTable(pedido);
                    });
                }
            }
        } catch {
            // Silently ignore network hiccup; next poll will retry smoothly
        } finally {
            isFetching = false;
        }
    }

    // Initial check to calibrate ultimoId
    checkNuevosPedidos();

    // Check periodically: every 5 seconds when visible, every 15s when hidden
    setInterval(() => {
        if (!document.hidden) {
            checkNuevosPedidos();
        }
    }, 5000);

    // Immediate check when tab is focused
    window.addEventListener('focus', checkNuevosPedidos);
});
