/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  safelist: [
    // Golden Ratio Typography
    'text-hero', 'text-display', 'text-h1', 'text-h2', 'text-h3', 
    'text-body-lg', 'text-body', 'text-small', 'text-caption',
    // Golden Ratio Spacing - Padding
    { pattern: /^p-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^px-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^py-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^pt-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^pb-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^pl-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^pr-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    // Golden Ratio Spacing - Margin
    { pattern: /^m-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^mx-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^my-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^mt-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^mb-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^ml-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^mr-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    // Golden Ratio Spacing - Gap
    { pattern: /^gap-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^gap-x-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^gap-y-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    // Golden Ratio Spacing - Space
    { pattern: /^space-x-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
    { pattern: /^space-y-gr-(xs|sm|md|lg|xl|2xl|3xl)$/ },
  ],
  theme: {
    // Golden Ratio Typography Scale (φ = 1.618)
    fontSize: {
      'hero': ['64px', { lineHeight: '1.1', fontWeight: '700' }],
      'display': ['52px', { lineHeight: '1.15', fontWeight: '700' }],
      'h1': ['40px', { lineHeight: '1.2', fontWeight: '700' }],
      'h2': ['32px', { lineHeight: '1.3', fontWeight: '600' }],
      'h3': ['24px', { lineHeight: '1.4', fontWeight: '600' }],
      'body-lg': ['18px', { lineHeight: '1.6', fontWeight: '400' }],
      'body': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
      'small': ['14px', { lineHeight: '1.5', fontWeight: '500' }],
      'caption': ['12px', { lineHeight: '1.4', fontWeight: '400' }],
      // Keep Tailwind defaults too
      'xs': ['.75rem', { lineHeight: '1rem' }],
      'sm': ['.875rem', { lineHeight: '1.25rem' }],
      'base': ['1rem', { lineHeight: '1.5rem' }],
      'lg': ['1.125rem', { lineHeight: '1.75rem' }],
      'xl': ['1.25rem', { lineHeight: '1.75rem' }],
      '2xl': ['1.5rem', { lineHeight: '2rem' }],
      '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
      '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
    },
    // Golden Ratio Spacing Scale (based on Fibonacci sequence)
    spacing: {
      'gr-xs': '8px',
      'gr-sm': '13px',
      'gr-md': '21px',
      'gr-lg': '34px',
      'gr-xl': '55px',
      'gr-2xl': '89px',
      'gr-3xl': '144px',
      // Keep Tailwind defaults
      '0': '0px',
      'px': '1px',
      '0.5': '0.125rem',
      '1': '0.25rem',
      '2': '0.5rem',
      '3': '0.75rem',
      '4': '1rem',
      '5': '1.25rem',
      '6': '1.5rem',
      '8': '2rem',
      '10': '2.5rem',
      '12': '3rem',
      '16': '4rem',
      '20': '5rem',
      '24': '6rem',
      '32': '8rem',
      '40': '10rem',
      '48': '12rem',
      '56': '14rem',
      '64': '16rem',
      '72': '18rem',
    },
    extend: {
      colors: {
        // LGU1 Brand Colors (from your original design)
        'lgu-primary': '#00473e',    // Dark green
        'lgu-secondary': '#ffa8ba',  // Pink accent (CORRECTED from PROJECT_DESIGN_RULES.md)
        'lgu-light': '#f2f7f5',      // Light mint background
        'lgu-white': '#ffffff',
        // Sidebar specific colors
        'lgu-bg': '#f2f7f5',
        'lgu-headline': '#00473e',
        'lgu-paragraph': '#475d5b',
        'lgu-button': '#faae2b',
        'lgu-button-text': '#00473e',
        'lgu-stroke': '#00332c',
        'lgu-main': '#f2f7f5',
        'lgu-highlight': '#faae2b',
        'lgu-tertiary': '#fa5246',
        'lgu-gray': {
          50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937',
          900: '#111827',
        },
      },
      fontFamily: {
        'sans': ['Poppins', 'sans-serif'],  // Default font for all text (PROJECT_DESIGN_RULES.md)
        'poppins': ['Poppins', 'sans-serif'],
      },
      // Golden Ratio Line Heights
      lineHeight: {
        'golden': '1.618',
        'golden-relaxed': '1.75',
      },
      // Golden Ratio based widths for optimal reading
      maxWidth: {
        'reading': '65ch',  // Optimal: 45-75 characters per line
        'golden-sm': '380px',  // ~23.6% of 1600px
        'golden-md': '618px',  // φ × 380
        'golden-lg': '1000px', // φ × 618
      },
      backgroundImage: {
        'radial-gradient': 'radial-gradient(circle at top left, #00473e, #003830, #002d28)',
      },
    },
  },
  plugins: [],
}

