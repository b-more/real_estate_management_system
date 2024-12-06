import preset from '../../../../vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#198754',    // Zambian Green
                secondary: '#CE1126',   // Zambian Red
                accent: '#FF8C00',      // Copper/Orange
                'accent-dark': '#D67A00', // Darker Copper
                background: '#F0F8F4',  // Light green background
                'background-dark': '#1a1a1a', // Dark mode background
                success: '#198754',
                warning: '#FF8C00',
                danger: '#CE1126',
            }
        }
    }
}