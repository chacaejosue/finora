document.addEventListener('DOMContentLoaded', () => {
    const productItems = document.querySelectorAll('.product-item');
    const filterChips = document.querySelectorAll('.filter-chip');
    const searchInput = document.getElementById('catalog-search');
    const headerSearchInput = document.getElementById('headerSearchInput');
    const emptyState = document.getElementById('landingEmptyCatalog');
    const emptyMessage = document.getElementById('landingEmptyMessage');

    let currentCategory = 'all';
    let searchQuery = '';

    function updateVisibility() {
        let visibleCount = 0;
        productItems.forEach(item => {
            const itemCat = item.getAttribute('data-cat') || '';
            const itemText = (item.getAttribute('data-nombre') || '' + ' ' + (item.getAttribute('data-desc') || '') + ' ' + (item.getAttribute('data-categoria-nombre') || '')).toLowerCase();

            const catMatch = currentCategory === 'all' || itemCat === currentCategory;
            const textMatch = !searchQuery || itemText.includes(searchQuery);

            if (catMatch && textMatch) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
        if (emptyMessage && visibleCount === 0) {
            emptyMessage.textContent = currentCategory !== 'all'
                ? 'No encontramos productos con esa búsqueda en la categoría seleccionada.'
                : 'No encontramos productos que coincidan con tu búsqueda.';
        }
    }

    // Filtros por categoría
    filterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            filterChips.forEach(c => {
                c.classList.remove('bg-sky-500', 'text-white', 'shadow-md', 'active');
                c.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border-slate-200');
            });
            chip.classList.add('bg-sky-500', 'text-white', 'shadow-md', 'active');
            chip.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border-slate-200');

            currentCategory = chip.getAttribute('data-cat') || 'all';
            updateVisibility();
        });
    });

    // Enlaces de categorías superiores ("Explora ->")
    document.querySelectorAll('[data-filter-category]').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetCat = link.getAttribute('data-filter-category');
            if (targetCat) {
                const matchingChip = document.querySelector(`.filter-chip[data-cat="${targetCat}"]`);
                if (matchingChip) {
                    matchingChip.click();
                    document.getElementById('catalogo-destacados')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Buscador principal
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            if (headerSearchInput && headerSearchInput.value !== e.target.value) {
                headerSearchInput.value = e.target.value;
            }
            updateVisibility();
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('catalogo-destacados')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Buscador del header
    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            if (searchInput && searchInput.value !== e.target.value) {
                searchInput.value = e.target.value;
            }
            updateVisibility();
        });
    }

    // Favoritos / Wishlist Hearts (con persistencia en LocalStorage)
    const storedWishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
    document.querySelectorAll('.wishlist-toggle-btn').forEach(btn => {
        const prodId = btn.getAttribute('data-prod-id');
        const icon = btn.querySelector('.heart-icon');

        if (storedWishlist.includes(prodId)) {
            btn.classList.add('is-active', 'text-rose-500');
            btn.classList.remove('text-slate-400');
            if (icon) icon.classList.add('fill-current');
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            let wishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
            const index = wishlist.indexOf(prodId);

            if (index > -1) {
                wishlist.splice(index, 1);
                btn.classList.remove('is-active', 'text-rose-500');
                btn.classList.add('text-slate-400');
                if (icon) icon.classList.remove('fill-current');
            } else {
                wishlist.push(prodId);
                btn.classList.add('is-active', 'text-rose-500');
                btn.classList.remove('text-slate-400');
                if (icon) icon.classList.add('fill-current');

                // Pequeña animación pop
                btn.classList.add('scale-125');
                setTimeout(() => btn.classList.remove('scale-125'), 250);
            }
            localStorage.setItem('finora-favoritos', JSON.stringify(wishlist));
        });
    });

    // Suscripción al Newsletter
    const newsletterForm = document.getElementById('newsletterForm');
    const newsletterBtn = document.getElementById('newsletterBtn');
    const newsletterInput = document.getElementById('newsletterInput');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = newsletterInput?.value.trim();
            if (!email) return;

            if (newsletterBtn) {
                const originalHtml = newsletterBtn.innerHTML;
                newsletterBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> ¡Suscrito!';
                newsletterBtn.classList.remove('bg-slate-900', 'hover:bg-slate-800');
                newsletterBtn.classList.add('bg-emerald-600', 'text-white');
                newsletterBtn.disabled = true;

                if (newsletterInput) newsletterInput.value = '';

                setTimeout(() => {
                    newsletterBtn.innerHTML = originalHtml;
                    newsletterBtn.classList.remove('bg-emerald-600', 'text-white');
                    newsletterBtn.classList.add('bg-slate-900', 'hover:bg-slate-800');
                    newsletterBtn.disabled = false;
                }, 4000);
            }
        });
    }

    // Apertura de Modales de Políticas desde cualquier lugar
    window.finoraOpenPolicy = function(type) {
        const dialogMap = {
            'faq': 'policyModalFaq',
            'envios': 'policyModalEnvios',
            'terminos': 'policyModalTerminos',
            'privacidad': 'policyModalPrivacidad'
        };
        const dialogId = dialogMap[type];
        if (dialogId) {
            const dialog = document.getElementById(dialogId);
            if (dialog) dialog.showModal();
        }
    };

    // Cerrar modales de políticas al hacer click en el backdrop
    document.querySelectorAll('.finora-policy-dialog').forEach(dialog => {
        dialog.addEventListener('click', (e) => {
            const rect = dialog.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right ||
                e.clientY < rect.top  || e.clientY > rect.bottom) {
                dialog.close();
            }
        });
    });
});
