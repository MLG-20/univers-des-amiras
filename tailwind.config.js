import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Substituts libres de Canela et Neue Haas Grotesk, qui sont sous
                // licence payante (cf. docs/progress/02.1-reidentite-aissatou-store.md).
                // Basculer ici suffira si la cliente achète les licences un jour.
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            // Palette « Atelier Nocturne » (rapport d'identité Aissatou Store V3).
            // Les jetons sont nommés par RÔLE et non par couleur : un futur
            // changement d'identité ne touchera que ce fichier, pas les vues.
            colors: {
                brand: {
                    ink: '#17151B',        // Texte et ancrage — 38 % de la surface
                    signature: '#4A1833',  // Cassis laqué, profondeur — 25 %
                    parchment: '#F4E6D5',  // Fond de section, respiration — 20 %
                    sage: '#A7AE91',       // Contrepoint textile — 10 %
                    accent: '#9F2D40',     // Rouge garance, signal et focus — 7 % MAX
                    // Deux nuances de service absentes du rapport, dérivées pour
                    // les besoins de l'interface :
                    surface: '#FBF7F1',    // Fond de page — le Parchemin pur en pleine
                                           // page écrase le contenu ; version éclaircie.
                    muted: '#6E6672',      // Texte secondaire — Encre désaturée, le
                                           // rapport ne fournit aucun gris.
                },
            },
        },
    },

    plugins: [forms],
};
