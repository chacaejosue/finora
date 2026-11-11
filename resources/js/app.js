import './landing.js';
import './realtime-orders.js';

// Soporte para transiciones suaves nativas entre páginas (Cross-Document View Transitions)
if (!document.querySelector('meta[name="view-transition"]')) {
    const vtMeta = document.createElement('meta');
    vtMeta.name = 'view-transition';
    vtMeta.content = 'same-origin';
    document.head.appendChild(vtMeta);
}

// =========================================================================
// Precarga predictiva universal (Landing, Categorías con filtros, Ofertas y Panel)
// =========================================================================
const prefetchCache = new Map();
const nativePrefetchedUrls = new Set();
let currentNavAbortController = null;

function prefetchRoute(href) {
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
    try {
        const targetUrl = new URL(href, window.location.origin);
        if (targetUrl.origin !== window.location.origin) return;
        if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) return;

        const urlString = targetUrl.href;

        // 1. Precarga en memoria (prefetchCache) para intercambio SPA instantáneo en toda la aplicación
        if (!prefetchCache.has(urlString)) {
            fetch(urlString, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.ok ? res.text() : null)
            .then(html => {
                if (html) {
                    prefetchCache.set(urlString, html);
                    if (prefetchCache.size > 40) {
                        const oldest = prefetchCache.keys().next().value;
                        prefetchCache.delete(oldest);
                    }
                }
            })
            .catch(() => {});
        }

        // 2. Precarga nativa del navegador mediante <link rel="prefetch">
        if (!nativePrefetchedUrls.has(urlString)) {
            nativePrefetchedUrls.add(urlString);

            const prefetchLink = document.createElement('link');
            prefetchLink.rel = 'prefetch';
            prefetchLink.href = urlString;
            prefetchLink.as = 'document';
            document.head.appendChild(prefetchLink);
        }
    } catch {}
}

// Activar reglas de especulación si el navegador lo soporta (Chrome / Edge)
try {
    if (HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) {
        const specScript = document.createElement('script');
        specScript.type = 'speculationrules';
        specScript.textContent = JSON.stringify({
            prefetch: [
                {
                    source: 'list',
                    urls: ['/categorias', '/ofertas', '/carrito'],
                    eagerness: 'moderate'
                }
            ]
        });
        document.head.appendChild(specScript);
    }
} catch {}

// Escuchar eventos de cursor y touch para precargar predictivamente
document.addEventListener('pointerenter', (e) => {
    const link = e.target.closest('a[href]');
    if (link) {
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        prefetchRoute(link.getAttribute('href'));
        return;
    }

    // Botones de cambio rápido de categoría y filtros
    const switchBtn = e.target.closest('[data-switch-category]');
    if (switchBtn) {
        const cat = switchBtn.getAttribute('data-switch-category');
        if (cat) {
            prefetchRoute('/categorias?categoria=' + encodeURIComponent(cat));
        }
        return;
    }

    const catFilterBtn = e.target.closest('.category-filter[data-category]');
    if (catFilterBtn && window.finoraIsServerFiltered) {
        const cat = catFilterBtn.dataset.category;
        if (cat === 'all') prefetchRoute('/categorias');
        else if (cat === 'ofertas') prefetchRoute('/ofertas');
        else if (cat === 'en-stock') prefetchRoute('/categorias?en_stock=1');
        else if (cat) prefetchRoute('/categorias?categoria=' + encodeURIComponent(cat));
    }
}, { passive: true, capture: true });

document.addEventListener('touchstart', (e) => {
    const link = e.target.closest('a[href]');
    if (link && link.target !== '_blank' && !link.hasAttribute('download')) {
        prefetchRoute(link.getAttribute('href'));
    }
}, { passive: true });

// Determinar zona estructural de la ruta para garantizar compatibilidad de layouts
function getFinoraRouteZone(pathname) {
    if (pathname.startsWith('/panel') || pathname.startsWith('/contabilidad')) {
        return 'panel';
    }
    if (pathname === '/login' || pathname === '/logout') {
        return 'auth';
    }
    if (pathname.startsWith('/mi-cuenta')) {
        return 'client';
    }
    // Tienda pública (landing /, /categorias, /ofertas, /carrito, confirmacion de pedido)
    return 'store';
}

// Navegación fluida e instantánea universal (Panel, Landing, Categorías, Filtros y Ofertas)
async function executeSpaNavigation(targetUrl, replaceHistory = false) {
    if (currentNavAbortController) {
        currentNavAbortController.abort();
    }
    currentNavAbortController = new AbortController();
    const signal = currentNavAbortController.signal;

    try {
        const currentZone = getFinoraRouteZone(window.location.pathname);
        const targetZone = getFinoraRouteZone(targetUrl.pathname);

        // Si se cambia entre zonas estructurales distintas o se está en auth/cliente, navegar nativamente
        if (currentZone !== targetZone || currentZone === 'auth' || currentZone === 'client') {
            window.location.href = targetUrl.href;
            return;
        }

        let html = prefetchCache.get(targetUrl.href);

        if (!html) {
            const response = await fetch(targetUrl.href, {
                signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                window.location.href = targetUrl.href;
                return;
            }

            html = await response.text();
            prefetchCache.set(targetUrl.href, html);
        }

        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');

        const currentMain = document.querySelector('main');
        const newMain = newDoc.querySelector('main');

        if (!currentMain || !newMain) {
            window.location.href = targetUrl.href;
            return;
        }

        const isCurrentPanel = (currentZone === 'panel');

        const performSwap = () => {
            document.title = newDoc.title;

            // 1. Sincronizar encabezados y menús según el entorno
            if (isCurrentPanel) {
                const currentNav = document.getElementById('panelNavbarContainer');
                const newNav = newDoc.getElementById('panelNavbarContainer');
                const currentDrawer = document.getElementById('panelDrawer');
                const newDrawer = newDoc.getElementById('panelDrawer');

                if (currentNav && newNav) currentNav.innerHTML = newNav.innerHTML;
                if (currentDrawer && newDrawer) currentDrawer.innerHTML = newDrawer.innerHTML;
            } else {
                const currentHeader = document.getElementById('mainHeader');
                const newHeader = newDoc.getElementById('mainHeader');
                if (currentHeader && newHeader) {
                    currentHeader.innerHTML = newHeader.innerHTML;
                }

                const currentFooter = document.querySelector('footer');
                const newFooter = newDoc.querySelector('footer');
                if (currentFooter && newFooter) {
                    currentFooter.innerHTML = newFooter.innerHTML;
                }
            }

            // 2. Intercambio atómico del contenedor principal (<main>)
            currentMain.replaceWith(newMain);

            // 3. Manejo de posición de scroll
            if (targetUrl.hash) {
                const hashId = targetUrl.hash.replace('#', '');
                const targetEl = document.getElementById(hashId);
                if (targetEl) {
                    setTimeout(() => targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' }), 60);
                } else {
                    window.scrollTo({ top: 0, behavior: 'instant' });
                }
            } else {
                window.scrollTo({ top: 0, behavior: 'instant' });
            }

            // 4. Interceptar y ejecutar scripts de la nueva vista
            const origAddEventListener = document.addEventListener;
            const pageInitCallbacks = [];
            document.addEventListener = function(type, listener, options) {
                if (type === 'DOMContentLoaded') {
                    pageInitCallbacks.push(listener);
                } else {
                    origAddEventListener.call(document, type, listener, options);
                }
            };

            // Scripts dentro de <main>
            newMain.querySelectorAll('script').forEach((oldScript) => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Scripts fuera de main (ej: variables inline window.finoraCatalogProducts en categorias.blade.php o scripts de landing)
            newDoc.body.querySelectorAll('script').forEach((oldScript) => {
                if (oldScript.src && (oldScript.src.includes('/build/') || oldScript.src.includes('tailwindcss') || oldScript.src.includes('fonts.googleapis'))) return;
                if (newMain.contains(oldScript)) return;
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
                newScript.remove();
            });

            document.addEventListener = origAddEventListener;

            pageInitCallbacks.forEach(cb => {
                try { cb(); } catch (err) { console.error('Error inicializando vista:', err); }
            });

            // 5. Reinicializar módulos de la interfaz pública
            if (!isCurrentPanel) {
                window.finoraInitHeader?.();
                window.finoraInitLanding?.();
                window.finoraInitCatalogFilters?.();
                window.finoraInitLikes?.();
            }

            window.dispatchEvent(new CustomEvent('finora:navigated', { detail: { url: targetUrl.href } }));
        };

        if (document.startViewTransition) {
            document.startViewTransition(() => {
                performSwap();
            });
        } else {
            performSwap();
        }

        if (replaceHistory) {
            window.history.replaceState({}, '', targetUrl.href);
        } else {
            window.history.pushState({}, '', targetUrl.href);
        }
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.warn('Fallo en transición rápida, navegando nativo:', err);
            window.location.href = targetUrl.href;
        }
    }
}

window.finoraNavigateSpa = function(url) {
    try {
        const targetUrl = new URL(url, window.location.origin);
        return executeSpaNavigation(targetUrl, false);
    } catch {
        window.location.href = url;
    }
};

function initUniversalSpaNavigation() {
    document.addEventListener('click', async (e) => {
        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

        let targetUrl;
        try {
            targetUrl = new URL(href, window.location.origin);
        } catch {
            return;
        }

        if (targetUrl.origin !== window.location.origin) return;

        const currentZone = getFinoraRouteZone(window.location.pathname);
        const targetZone = getFinoraRouteZone(targetUrl.pathname);

        // Rutas que requieren recarga completa (auth/login, logout, o cruce entre zonas de layout distintas)
        if (currentZone !== targetZone || currentZone === 'auth' || currentZone === 'client') {
            return;
        }

        // Si la URL es la misma (pathname + query), ignorar o hacer scroll si tiene ancla
        if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) {
            if (targetUrl.hash) {
                const targetEl = document.querySelector(targetUrl.hash);
                if (targetEl) {
                    e.preventDefault();
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    history.pushState(null, '', targetUrl.hash);
                }
            }
            return;
        }

        e.preventDefault();
        await executeSpaNavigation(targetUrl, false);
    });

    window.addEventListener('popstate', async () => {
        try {
            const targetUrl = new URL(window.location.href);
            await executeSpaNavigation(targetUrl, true);
        } catch {
            window.location.reload();
        }
    });
}

initUniversalSpaNavigation();

const themeStorageKey = 'finora-theme-v2';
const storedTheme = window.localStorage.getItem(themeStorageKey);

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;
}

applyTheme(storedTheme === 'dark' ? 'dark' : 'light');
document.documentElement.classList.add('theme-ready');

document.addEventListener('DOMContentLoaded', () => {
    // Evitar cualquier duplicación del botón de cambio de tema (luna/sol)
    if (document.querySelector('.finora-theme-toggle')) {
        return;
    }

    const themeToggle = document.createElement('button');
    const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

    themeToggle.type = 'button';
    themeToggle.className = 'finora-theme-toggle';
    themeToggle.setAttribute('aria-label', currentTheme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    themeToggle.setAttribute('title', themeToggle.getAttribute('aria-label'));
    themeToggle.innerHTML = `<span class="material-symbols-outlined" aria-hidden="true">${currentTheme === 'dark' ? 'light_mode' : 'dark_mode'}</span>`;

    themeToggle.addEventListener('click', () => {
        const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(nextTheme);
        window.localStorage.setItem(themeStorageKey, nextTheme);
        const label = nextTheme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
        themeToggle.setAttribute('aria-label', label);
        themeToggle.setAttribute('title', label);
        themeToggle.querySelector('span').textContent = nextTheme === 'dark' ? 'light_mode' : 'dark_mode';
    });

    let floatingButtons = document.getElementById('floating-buttons');

    // Menú desplegable de perfil de usuario en el panel
    document.addEventListener('click', (e) => {
        const btn = document.getElementById('userProfileBtn');
        const dropdown = document.getElementById('userProfileDropdown');
        if (!btn || !dropdown) return;

        if (btn.contains(e.target)) {
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden', !isHidden);
            btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        } else if (!dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('userProfileDropdown');
            const btn = document.getElementById('userProfileBtn');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                btn?.setAttribute('aria-expanded', 'false');
            }
        }
    });

    if (!floatingButtons && window.location.pathname.startsWith('/panel')) {
        floatingButtons = document.createElement('div');
        floatingButtons.id = 'floating-buttons';
        floatingButtons.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 transition-all duration-300 opacity-0 translate-y-4 pointer-events-none';
        floatingButtons.innerHTML = `
            <button id="backToTopPanelBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})"
                class="w-12 h-12 flex items-center justify-center rounded-full bg-surface-container-lowest shadow-lg border border-surface-container-high/60 text-secondary hover:bg-secondary hover:text-on-secondary transition-all hover:shadow-xl hover:scale-105 active:scale-95 cursor-pointer"
                aria-label="Volver arriba" title="Volver arriba">
                <span class="material-symbols-outlined text-[22px]">arrow_upward</span>
            </button>
        `;
        document.body.appendChild(floatingButtons);
    }

    if (document.body.classList.contains('login-page')) {
        return;
    }

    const themeSlot = document.querySelector('.theme-toggle-slot');
    if (themeSlot) {
        themeSlot.appendChild(themeToggle);
        themeToggle.classList.add('order-last', 'inline-flex', 'items-center', 'justify-center', 'rounded-xl', 'p-2.5', 'text-on-surface-variant', 'hover:bg-surface-container', 'hover:text-secondary', 'transition-colors');
    } else if (window.location.pathname.startsWith('/panel')) {
        const panelHeaderActions = document.querySelector('header .flex.items-center.justify-between > div:last-child');
        if (panelHeaderActions) {
            panelHeaderActions.append(themeToggle);
            themeToggle.classList.add('order-last', 'inline-flex', 'items-center', 'justify-center', 'rounded-xl', 'p-2.5', 'text-on-surface-variant', 'hover:bg-surface-container', 'hover:text-secondary', 'transition-colors');
        } else if (floatingButtons) {
            floatingButtons.appendChild(themeToggle);
        }
    } else if (floatingButtons) {
        floatingButtons.appendChild(themeToggle);
    } else {
        document.body.appendChild(themeToggle);
    }

    if (floatingButtons && window.location.pathname.startsWith('/panel')) {
        const updateFloatingButtons = () => {
            const isVisible = window.scrollY > 220;
            floatingButtons.classList.toggle('opacity-0', !isVisible);
            floatingButtons.classList.toggle('translate-y-4', !isVisible);
            floatingButtons.classList.toggle('pointer-events-none', !isVisible);
            floatingButtons.classList.toggle('opacity-100', isVisible);
            floatingButtons.classList.toggle('translate-y-0', isVisible);
            floatingButtons.classList.toggle('pointer-events-auto', isVisible);
        };

        window.addEventListener('scroll', updateFloatingButtons, { passive: true });
        updateFloatingButtons();
    }

    if (document.getElementById('backToTopCatalog')) {
        themeToggle.classList.add('finora-theme-above-back-to-top');
    }

    document.querySelectorAll('[id="openPanelDrawerBtn"]').forEach(button => {
        button.setAttribute('aria-label', 'Abrir navegación de módulos');
        button.setAttribute('aria-controls', 'panelDrawer');
        button.setAttribute('aria-expanded', 'false');
    });

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            if (form.dataset.preventSubmitFeedback === 'true') return;

            form.classList.add('finora-form-submitting');
            form.setAttribute('aria-busy', 'true');

            form.querySelectorAll('button[type="submit"]').forEach(button => {
                button.disabled = true;
                button.setAttribute('data-original-label', button.getAttribute('aria-label') || '');
                button.setAttribute('aria-label', 'Procesando solicitud');
            });
        });
    });

    document.querySelectorAll('form label').forEach(label => {
        const field = label.querySelector('input, select, textarea');
        if (field && !field.required && !label.textContent.toLowerCase().includes('opcional')) {
            const optional = document.createElement('span');
            optional.className = 'ml-1 font-normal text-slate-400';
            optional.textContent = '(opcional)';
            label.appendChild(optional);
        }
    });

    if ((window.location.pathname.startsWith('/panel') || window.location.pathname.startsWith('/mi-cuenta')) && document.querySelector('form[action$="/logout"]')) {
        let lastActivity = Date.now();
        const markActivity = () => { lastActivity = Date.now(); };
        ['click', 'keydown', 'pointermove', 'scroll'].forEach(eventName => window.addEventListener(eventName, markActivity, {passive: true}));
        window.setInterval(() => {
            if (Date.now() - lastActivity >= 10 * 60 * 1000) {
                document.querySelector('form[action$="/logout"]')?.submit();
            }
        }, 30000);
    }

    window.finoraShowAdded = (button) => {
        if (!button || button.dataset.isAdding === 'true') return;
        button.dataset.isAdding = 'true';

        const originalContent = button.innerHTML;
        const originalBgClass = button.classList.contains('bg-[#00c1de]')
            ? 'bg-[#00c1de]'
            : (button.classList.contains('bg-finora-cyan') ? 'bg-finora-cyan' : '');
        const hasText = button.querySelector('span') !== null;

        button.classList.add('scale-95', 'bg-emerald-600', 'ring-2', 'ring-emerald-200');
        if (originalBgClass) {
            button.classList.remove(originalBgClass);
        }

        const checkSvg = `<svg class="w-4 h-4 fill-none stroke-current shrink-0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;

        if (hasText) {
            button.innerHTML = `${checkSvg}<span>¡Añadido!</span>`;
        } else {
            button.innerHTML = checkSvg;
        }
        button.setAttribute('aria-label', 'Producto añadido al carrito');

        window.setTimeout(() => {
            button.classList.remove('scale-95', 'bg-emerald-600', 'ring-2', 'ring-emerald-200');
            if (originalBgClass) {
                button.classList.add(originalBgClass);
            }
            button.innerHTML = originalContent;
            delete button.dataset.isAdding;
        }, 1350);
    };

    window.finoraShowToast = (message = '¡Producto añadido a tu pedido!', productName = '') => {
        let toastContainer = document.getElementById('finoraToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'finoraToastContainer';
            toastContainer.className = 'fixed bottom-24 right-4 sm:bottom-6 sm:right-6 z-[95] flex flex-col gap-2 pointer-events-none max-w-sm';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center gap-3 bg-[#0e1b2e]/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-xl border border-sky-400/25 transform transition-all duration-500 ease-out translate-y-4 opacity-0';
        toast.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
            </div>
            <div class="flex flex-col text-xs leading-tight">
                <span class="font-bold text-white">${message}</span>
                ${productName ? `<span class="text-slate-300 line-clamp-1 mt-0.5">${productName}</span>` : ''}
            </div>
            <a href="/carrito" class="ml-auto px-3 py-1.5 bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold text-[11px] rounded-full transition-colors shrink-0 whitespace-nowrap shadow-xs">
                Ver pedido →
            </a>
        `;
        toastContainer.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    };

    window.finoraAddProduct = (button) => {
        const productId = String(button.dataset.addProduct);
        const cart = JSON.parse(window.localStorage.getItem('finora-carrito') || '[]');
        const existing = cart.find(item => String(item.id) === productId);

        if (existing) existing.qty = Math.min(Number(existing.qty) + 1, 99);
        else cart.push({ id: productId, qty: 1 });

        window.localStorage.setItem('finora-carrito', JSON.stringify(cart));
        document.querySelectorAll('#publicCartBadge').forEach(badge => {
            badge.textContent = cart.reduce((total, item) => total + Number(item.qty || 0), 0);
            badge.classList.remove('hidden');
        });
        window.finoraShowAdded(button);
        const card = button.closest('.product-item, .catalog-product');
        const prodName = card?.dataset?.nombre || card?.querySelector('h2, h3')?.textContent?.trim() || '';
        window.finoraShowToast('¡Añadido a tu pedido!', prodName);

        // Precarga anticipada inmediata de la vista del carrito para que al hacer clic sea instantánea
        prefetchRoute('/carrito');

        if (card) {
            const flyOrb = document.createElement('span');
            flyOrb.className = 'finora-cart-orb fixed z-[100] pointer-events-none';
            const start = card.getBoundingClientRect();
            const cart = document.querySelector('[aria-label="Ver carrito"]') || document.getElementById('publicCartBadge')?.parentElement;
            const end = cart?.getBoundingClientRect();
            if (end) {
                flyOrb.style.left = `${start.left + start.width / 2 - 12}px`;
                flyOrb.style.top = `${start.top + start.height / 2 - 12}px`;
                document.body.appendChild(flyOrb);

                // Forzar reflujo de estilo para que el navegador inicie la transición suave desde la posición inicial
                void flyOrb.offsetWidth;

                requestAnimationFrame(() => {
                    flyOrb.style.left = `${end.left + end.width / 2 - 12}px`;
                    flyOrb.style.top = `${end.top + end.height / 2 - 12}px`;
                    flyOrb.classList.add('finora-cart-orb-flying');
                });
                setTimeout(() => {
                    flyOrb.remove();
                    // Pulso suave en el badge del carrito al llegar el orbe
                    document.querySelectorAll('#publicCartBadge').forEach(b => {
                        b.classList.add('scale-125');
                        setTimeout(() => b.classList.remove('scale-125'), 220);
                    });
                }, 850);
            }
        }
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-add-product]');
        if (!button || document.getElementById('publicOrderForm')) return;
        if (button.getAttribute('onclick')?.includes('finoraAddProduct')) return;

        event.preventDefault();
        window.finoraAddProduct(button);
    });

    // Likes comunitarios (corazones) con persistencia en MySQL y LocalStorage
    window.finoraToggleLike = async (button) => {
        const prodId = button.getAttribute('data-prod-id');
        if (!prodId) return;

        let wishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
        const isCurrentlyLiked = wishlist.includes(String(prodId)) || wishlist.includes(Number(prodId));
        const action = isCurrentlyLiked ? 'unlike' : 'like';

        if (isCurrentlyLiked) {
            wishlist = wishlist.filter(id => String(id) !== String(prodId));
        } else {
            wishlist.push(String(prodId));
        }
        localStorage.setItem('finora-favoritos', JSON.stringify(wishlist));

        // Actualizar visualmente todos los botones del producto en la página
        document.querySelectorAll(`.wishlist-toggle-btn[data-prod-id="${prodId}"]`).forEach(btn => {
            const icon = btn.querySelector('.heart-icon');
            if (action === 'like') {
                btn.classList.add('is-active', 'text-rose-500');
                btn.classList.remove('text-slate-400');
                if (icon) {
                    icon.classList.add('fill-rose-500', 'text-rose-500');
                    icon.setAttribute('fill', 'currentColor');
                }
                btn.classList.add('scale-125');
                setTimeout(() => btn.classList.remove('scale-125'), 250);
            } else {
                btn.classList.remove('is-active', 'text-rose-500');
                btn.classList.add('text-slate-400');
                if (icon) {
                    icon.classList.remove('fill-rose-500', 'text-rose-500');
                    icon.setAttribute('fill', 'none');
                }
            }
        });

        // Actualización optimista de contadores en pantalla
        document.querySelectorAll(`.likes-count[data-prod-id="${prodId}"]`).forEach(span => {
            let current = parseInt(span.textContent.trim(), 10) || 0;
            span.textContent = action === 'like' ? current + 1 : Math.max(0, current - 1);
        });

        // Llamada asíncrona al backend
        try {
            const res = await fetch(`/productos/${prodId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ action }),
            });
            if (res.ok) {
                const data = await res.json();
                if (data.likes !== undefined) {
                    document.querySelectorAll(`.likes-count[data-prod-id="${prodId}"]`).forEach(span => {
                        span.textContent = data.likes;
                    });
                }
            }
        } catch (e) {
            console.error('Error al registrar me gusta:', e);
        }
    };

    window.finoraInitLikes = () => {
        const wishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
        document.querySelectorAll('.wishlist-toggle-btn').forEach(btn => {
            const prodId = btn.getAttribute('data-prod-id');
            const icon = btn.querySelector('.heart-icon');
            if (wishlist.includes(String(prodId)) || wishlist.includes(Number(prodId))) {
                btn.classList.add('is-active', 'text-rose-500');
                btn.classList.remove('text-slate-400');
                if (icon) {
                    icon.classList.add('fill-rose-500', 'text-rose-500');
                    icon.setAttribute('fill', 'currentColor');
                }
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.finoraInitLikes);
    } else {
        window.finoraInitLikes();
    }

    // Alternar visibilidad de contraseña en la vista de inicio de sesión
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordInput && eyeIcon) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // Interacción y filtro reactivo para la vista de categorías y ofertas
    function initFinoraCatalogFilters() {
        const search = document.getElementById('catalogSearch');
        const filters = document.querySelectorAll('.category-filter');
        const products = document.querySelectorAll('.catalog-product');
        const count = document.getElementById('resultsCount');
        const empty = document.getElementById('emptyCatalog');
        const emptyMessage = document.getElementById('emptyCatalogMessage');
        const allCatalogProducts = window.finoraCatalogProducts || [];

        if (filters.length === 0) return;

        const navigateTo = (url) => {
            if (window.finoraNavigateSpa) {
                window.finoraNavigateSpa(url);
            } else {
                window.location.href = url;
            }
        };

        const urlParams = new URLSearchParams(window.location.search);
        let activeCategory = urlParams.get('categoria');
        if (!activeCategory) {
            if (urlParams.get('ofertas') === '1' || window.location.pathname.includes('/ofertas')) {
                activeCategory = 'ofertas';
            } else if (urlParams.get('en_stock') === '1' || urlParams.get('filtro') === 'stock') {
                activeCategory = 'en-stock';
            } else {
                activeCategory = 'all';
            }
        }

        if (![...filters].some(filter => filter.dataset.category === activeCategory)) {
            activeCategory = 'all';
        }

        const setActiveFilterStyles = () => {
            filters.forEach(filter => {
                const selected = filter.dataset.category === activeCategory;
                filter.setAttribute('aria-selected', String(selected));
                filter.classList.remove('is-active', 'is-active-oferta', 'is-active-stock', 'bg-emerald-600', 'text-white', 'border-emerald-600');
                if (selected) {
                    if (filter.dataset.category === 'ofertas') {
                        filter.classList.add('is-active-oferta');
                    } else if (filter.dataset.category === 'en-stock') {
                        filter.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                    } else {
                        filter.classList.add('is-active');
                    }
                    filter.classList.remove('bg-white', 'text-slate-700', 'text-rose-600', 'text-emerald-700', 'border-slate-200', 'border-rose-200/80', 'border-emerald-200/90');
                } else {
                    if (filter.dataset.category === 'ofertas') {
                        filter.classList.add('bg-white', 'text-rose-600', 'border', 'border-rose-200/80');
                    } else if (filter.dataset.category === 'en-stock') {
                        filter.classList.add('bg-white', 'text-emerald-700', 'border', 'border-emerald-200/90');
                    } else {
                        filter.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
                    }
                }
            });
        };

        function refreshCatalog() {
            const term = search ? search.value.trim().toLocaleLowerCase('es') : '';
            let visible = 0;
            products.forEach(product => {
                const isOffer = product.dataset.isOffer === 'true';
                const inStock = product.dataset.inStock === 'true';
                const matchesCategory = activeCategory === 'all'
                    ? true
                    : (activeCategory === 'ofertas'
                        ? isOffer
                        : (activeCategory === 'en-stock'
                            ? inStock
                            : product.dataset.productCategory === activeCategory));
                const matchesSearch = !term || product.textContent.toLocaleLowerCase('es').includes(term);
                const show = matchesCategory && matchesSearch;
                product.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (count) {
                count.textContent = `${visible} producto${visible === 1 ? '' : 's'}`;
            }
            if (empty) {
                empty.style.display = visible ? 'none' : 'block';
            }

            const suggestionBox = document.getElementById('emptyCategorySuggestion');
            const suggestedBtn = document.getElementById('suggestedCategoryBtn');

            if (emptyMessage && !visible) {
                const otherProduct = allCatalogProducts.find(product => term && product.nombre.toLocaleLowerCase('es').includes(term));
                
                if (otherProduct && otherProduct.categoria) {
                    const catSlug = otherProduct.categoria.toLowerCase().trim().replace(/[\s_]+/g, '-');
                    emptyMessage.textContent = `No encontramos "${term}" en este filtro, pero tenemos "${otherProduct.nombre}" y productos similares en la categoría ${otherProduct.categoria}.`;
                    if (suggestionBox && suggestedBtn) {
                        suggestionBox.classList.remove('hidden');
                        suggestedBtn.textContent = `Explorar ${otherProduct.categoria} →`;
                        suggestedBtn.onclick = () => {
                            const matchingFilter = document.querySelector(`.category-filter[data-category="${catSlug}"]`);
                            if (matchingFilter) {
                                matchingFilter.click();
                            } else {
                                window.finoraResetCatalogFilter?.();
                            }
                        };
                    }
                } else if (activeCategory === 'ofertas') {
                    emptyMessage.textContent = 'No encontramos productos en oferta con ese término específico, pero puedes explorar todo el catálogo general para ver más opciones disponibles.';
                    if (suggestionBox) suggestionBox.classList.add('hidden');
                } else {
                    emptyMessage.textContent = 'No encontramos productos con ese término exacto, pero puedes navegar por las diferentes categorías de belleza y cuidado personal.';
                    if (suggestionBox) suggestionBox.classList.add('hidden');
                }
            } else if (suggestionBox) {
                suggestionBox.classList.add('hidden');
            }

            // Controlar visibilidad del botón Limpiar Filtros y Limpiar Búsqueda
            const clearBtn = document.getElementById('clearFiltersBtn');
            const clearSearch = document.getElementById('clearSearchBtn');
            const hasActiveFilter = Boolean((term && term.length > 0) || (activeCategory !== 'all'));
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', !hasActiveFilter);
            }
            if (clearSearch) {
                clearSearch.classList.toggle('hidden', !(term && term.length > 0));
            }
        }

        window.finoraClearSearch = function() {
            if (search) search.value = '';
            const headerSearch = document.getElementById('headerSearchInput');
            if (headerSearch) headerSearch.value = '';
            const mobileHeaderSearch = document.getElementById('mobileHeaderSearchInput');
            if (mobileHeaderSearch) mobileHeaderSearch.value = '';
            refreshCatalog();
            search?.focus();
        };

        window.finoraResetCatalogFilter = function() {
            if (window.finoraIsServerFiltered) {
                navigateTo(window.finoraCatalogoUrl || '/categorias');
                return;
            }

            if (search) search.value = '';
            const headerSearch = document.getElementById('headerSearchInput');
            if (headerSearch) headerSearch.value = '';
            const mobileHeaderSearch = document.getElementById('mobileHeaderSearchInput');
            if (mobileHeaderSearch) mobileHeaderSearch.value = '';

            const activeFilterBanner = document.getElementById('activeFilterBanner');
            if (activeFilterBanner) {
                activeFilterBanner.classList.add('hidden');
            }

            if (window.history.replaceState) {
                window.history.replaceState(null, '', window.location.pathname);
            }

            activeCategory = 'all';
            setActiveFilterStyles();
            refreshCatalog();
        };

        document.querySelectorAll('.quick-category-switch').forEach(btn => {
            btn.addEventListener('click', () => {
                const cat = btn.getAttribute('data-switch-category');
                if (cat) {
                    if (window.finoraIsServerFiltered) {
                        navigateTo((window.finoraCatalogoUrl || '/categorias') + '?categoria=' + encodeURIComponent(cat));
                        return;
                    }
                    const matching = document.querySelector(`.category-filter[data-category="${cat}"]`);
                    if (matching) matching.click();
                }
            });
        });

        filters.forEach(button => {
            button.addEventListener('click', () => {
                const targetCat = button.dataset.category;

                // Si la página se cargó con filtro del servidor y no tiene todos los productos en el DOM,
                // navegar a la URL adecuada para que el backend entregue los productos correspondientes.
                if (window.finoraIsServerFiltered) {
                    if (targetCat === 'all') {
                        navigateTo(window.finoraCatalogoUrl || '/categorias');
                        return;
                    }
                    if (targetCat === 'ofertas' && !window.location.pathname.includes('/ofertas')) {
                        navigateTo(window.finoraOfertasUrl || '/ofertas');
                        return;
                    }
                    if (targetCat === 'en-stock') {
                        navigateTo((window.finoraCatalogoUrl || '/categorias') + '?en_stock=1');
                        return;
                    }
                    if (targetCat !== 'ofertas' && targetCat !== 'en-stock') {
                        const currentCatParam = new URLSearchParams(window.location.search).get('categoria');
                        if (currentCatParam !== targetCat) {
                            navigateTo((window.finoraCatalogoUrl || '/categorias') + '?categoria=' + encodeURIComponent(targetCat));
                            return;
                        }
                    }
                }

                activeCategory = targetCat;
                setActiveFilterStyles();
                refreshCatalog();
            });
        });

        if (search) {
            search.addEventListener('input', refreshCatalog);

            const headerSearch = document.getElementById('headerSearchInput');
            const mobileHeaderSearch = document.getElementById('mobileHeaderSearchInput');
            const headerForm = document.getElementById('headerSearchForm');
            const mobileHeaderForm = document.getElementById('mobileHeaderSearchForm');

            const handleCatHeaderSearch = (val) => {
                if (search.value !== val) {
                    search.value = val;
                    refreshCatalog();
                    if (val.trim().length >= 1) {
                        document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            };

            if (headerSearch) {
                headerSearch.addEventListener('input', (e) => handleCatHeaderSearch(e.target.value));
            }
            if (mobileHeaderSearch) {
                mobileHeaderSearch.addEventListener('input', (e) => handleCatHeaderSearch(e.target.value));
            }
            if (headerForm) {
                headerForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
            if (mobileHeaderForm) {
                mobileHeaderForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    document.getElementById('mobileMenuDrawer')?.classList.add('hidden');
                    document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        }

        setActiveFilterStyles();
        refreshCatalog();
    }

    window.finoraInitCatalogFilters = initFinoraCatalogFilters;
    initFinoraCatalogFilters();

    // Auto-dismiss y botón de cierre para mensajes flash de alerta
    const alerts = document.querySelectorAll('div[role="alert"]');
    alerts.forEach(alert => {
        // Evitar aplicar a la caja de error del login que se maneja via ajax
        if (alert.id === 'loginErrorBox' || alert.id === 'form-validation-alert') return;
            alert.classList.add('finora-toast', 'relative', 'overflow-hidden');

        if (!alert.querySelector('.alert-close-btn')) {
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'alert-close-btn ml-auto p-1 text-slate-400 hover:text-slate-700 rounded-lg transition-colors cursor-pointer shrink-0';
            closeBtn.innerHTML = '<span class="material-symbols-outlined text-sm">close</span>';
            closeBtn.setAttribute('aria-label', 'Cerrar notificación');
            closeBtn.addEventListener('click', () => {
                alert.classList.add('hide-toast');
                setTimeout(() => alert.remove(), 300);
            });
            alert.appendChild(closeBtn);
        }

        // Auto-dismiss suave tras 5 segundos
        setTimeout(() => {
            if (alert && alert.parentElement) {
                alert.classList.add('hide-toast');
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });

    const optionalLabelObserver = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                const labels = [
                    ...(node.matches?.('label') ? [node] : []),
                    ...(node.querySelectorAll?.('label') || []),
                ];
                labels.forEach(label => {
                    const field = label.querySelector('input, select, textarea');
                    if (field && !field.required && !label.textContent.toLowerCase().includes('opcional')) {
                        const optional = document.createElement('span');
                        optional.className = 'ml-1 font-normal text-slate-400';
                        optional.textContent = '(opcional)';
                        label.appendChild(optional);
                    }
                });
            });
        });
    });
    optionalLabelObserver.observe(document.body, { childList: true, subtree: true });

    // Vista previa interactiva al seleccionar una imagen de producto
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;

            let previewContainer = input.parentElement.querySelector('.image-file-preview');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'image-file-preview mt-3 flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-xl animate-scale-up';
                input.parentElement.appendChild(previewContainer);
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Vista previa" class="w-14 h-14 object-cover rounded-lg border border-slate-300 shrink-0">
                    <div class="flex flex-col text-xs text-slate-600 truncate">
                        <span class="font-bold text-finora-navy truncate">${file.name}</span>
                        <span class="text-[10px] text-slate-400">${(file.size / 1024).toFixed(1)} KB</span>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    });
});
