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

    // Buscadores (Header escritorio, Header móvil y catálogo principal)
    const mobileHeaderSearchInput = document.getElementById('mobileHeaderSearchInput');
    const headerSearchForm = document.getElementById('headerSearchForm');
    const mobileHeaderSearchForm = document.getElementById('mobileHeaderSearchForm');

    let scrollTimeout = null;
    function smoothScrollToProducts() {
        const catalogo = document.getElementById('catalogo-destacados');
        if (!catalogo) return;
        const rect = catalogo.getBoundingClientRect();
        // Si no estamos ya enfocados en la sección de productos filtrados, bajar automáticamente
        if (rect.top > 140 || rect.top < -60) {
            catalogo.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function handleSearchInput(value, sourceInput) {
        searchQuery = value.toLowerCase().trim();

        // Sincronizar todos los campos de búsqueda
        if (headerSearchInput && sourceInput !== headerSearchInput) {
            headerSearchInput.value = value;
        }
        if (mobileHeaderSearchInput && sourceInput !== mobileHeaderSearchInput) {
            mobileHeaderSearchInput.value = value;
        }
        if (searchInput && sourceInput !== searchInput) {
            searchInput.value = value;
        }

        updateVisibility();

        // Al buscar algo, bajar automáticamente a la zona donde se filtran los productos
        if (searchQuery.length >= 1) {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(smoothScrollToProducts, 280);
        }
    }

    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', (e) => {
            handleSearchInput(e.target.value, headerSearchInput);
        });

        headerSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(scrollTimeout);
                smoothScrollToProducts();
            }
        });
    }

    if (mobileHeaderSearchInput) {
        mobileHeaderSearchInput.addEventListener('input', (e) => {
            handleSearchInput(e.target.value, mobileHeaderSearchInput);
        });

        mobileHeaderSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(scrollTimeout);
                const mobileDrawer = document.getElementById('mobileMenuDrawer');
                if (mobileDrawer) mobileDrawer.classList.add('hidden');
                smoothScrollToProducts();
            }
        });
    }

    if (headerSearchForm) {
        headerSearchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearTimeout(scrollTimeout);
            smoothScrollToProducts();
        });
    }

    if (mobileHeaderSearchForm) {
        mobileHeaderSearchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearTimeout(scrollTimeout);
            const mobileDrawer = document.getElementById('mobileMenuDrawer');
            if (mobileDrawer) mobileDrawer.classList.add('hidden');
            smoothScrollToProducts();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            handleSearchInput(e.target.value, searchInput);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                smoothScrollToProducts();
            }
        });
    }

    // Favoritos / Wishlist Hearts (con persistencia en LocalStorage y MySQL)
    if (window.finoraInitLikes) {
        window.finoraInitLikes();
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
