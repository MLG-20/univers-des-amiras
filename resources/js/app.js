

import Alpine from 'alpinejs';
import productFilters from './shop-filters';

window.Alpine = Alpine;

Alpine.data('productFilters', productFilters);

Alpine.start();
