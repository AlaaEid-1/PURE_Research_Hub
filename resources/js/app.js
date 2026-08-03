import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    let isDarkMode = false;
    try {
        isDarkMode = localStorage.getItem('darkMode') === 'true' || 
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    } catch (e) {
        isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    Alpine.store('darkMode', {
        on: isDarkMode,
        toggle() {
            this.on = !this.on;
            try {
                localStorage.setItem('darkMode', this.on);
            } catch (e) {}
            
            if (this.on) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        init() {
            if (this.on) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
});

console.log('Starting AlpineJS...');
try {
    Alpine.start();
    console.log('AlpineJS started successfully.');
} catch (e) {
    console.error('Error starting AlpineJS:', e);
}
