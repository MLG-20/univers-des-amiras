// Recherche/filtres réactifs du catalogue public (Phase 2, section 2.1 et 4 du
// cahier des charges) : on récupère juste la grille de produits en HTML via
// fetch plutôt qu'un rechargement complet de page, en gardant tout le rendu
// côté serveur (aucune logique d'affichage dupliquée en JS).
export default function productFilters({ action, initial }) {
    return {
        filters: { ...initial },

        init() {
            this.bindPaginationLinks();
            window.addEventListener('popstate', () => this.apply(window.location.href, false));
        },

        buildUrl() {
            const params = new URLSearchParams();

            Object.entries(this.filters).forEach(([key, value]) => {
                if (value === '' || value === null || value === undefined || value === false) {
                    return;
                }

                params.set(key, value === true ? '1' : value);
            });

            const query = params.toString();

            return query ? `${action}?${query}` : action;
        },

        async apply(url = null, pushState = true) {
            const target = url ?? this.buildUrl();

            const response = await fetch(target, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                return;
            }

            this.$refs.grid.innerHTML = await response.text();
            this.bindPaginationLinks();

            if (pushState) {
                window.history.pushState({}, '', target);
            }
        },

        bindPaginationLinks() {
            // Uniquement les liens de pagination (conteneur [data-pagination]) —
            // surtout PAS les liens des cartes produit, qui doivent naviguer
            // normalement vers la fiche produit.
            this.$refs.grid.querySelectorAll('[data-pagination] a[href]').forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.apply(link.getAttribute('href'));
                });
            });
        },
    };
}
