/**
 * Modern Theme Management System
 * Handles light/dark mode switching with localStorage persistence
 */

class ThemeManager {
    constructor() {
        this.theme = this.getStoredTheme() || this.getSystemTheme();
        this.init();
    }

    init() {
        this.setTheme(this.theme);
        this.setupEventListeners();
        this.observeSystemThemeChanges();
    }

    getStoredTheme() {
        return localStorage.getItem('theme');
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    setTheme(theme) {
        this.theme = theme;
        localStorage.setItem('theme', theme);
        
        // Update document class
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Update theme toggle buttons
        this.updateToggleButtons();
        
        // Dispatch custom event for other components to listen
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    toggleTheme() {
        const newTheme = this.theme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
        
        // Add transition class for smooth animation
        document.body.classList.add('transition-colors', 'duration-300');
        setTimeout(() => {
            document.body.classList.remove('transition-colors', 'duration-300');
        }, 300);
    }

    updateToggleButtons() {
        const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
        toggleButtons.forEach(button => {
            const sunIcon = button.querySelector('.theme-icon-sun');
            const moonIcon = button.querySelector('.theme-icon-moon');
            
            if (this.theme === 'dark') {
                sunIcon?.classList.remove('hidden');
                moonIcon?.classList.add('hidden');
            } else {
                sunIcon?.classList.add('hidden');
                moonIcon?.classList.remove('hidden');
            }
        });
    }

    setupEventListeners() {
        // Theme toggle button listeners
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-theme-toggle]')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Keyboard shortcut for theme toggle (Ctrl/Cmd + Shift + T)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                this.toggleTheme();
            }
        });
    }

    observeSystemThemeChanges() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Only auto-switch if no theme is stored (user hasn't manually set preference)
            if (!this.getStoredTheme()) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    getCurrentTheme() {
        return this.theme;
    }

    isSystemDarkMode() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    // Method to reset to system preference
    resetToSystemTheme() {
        localStorage.removeItem('theme');
        this.setTheme(this.getSystemTheme());
    }
}

// Initialize theme manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.themeManager = new ThemeManager();
});

// Export for module usage
export default ThemeManager;