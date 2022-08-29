/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.php',
    './resources/css/*.css',
    './resources/js/components/*.js'
  ],
  theme: {
    extend: {
      colors: {
        primary: '#595959',
        secondary: '#e6e6e6',
        tertiary: '#eee',
        highlight: '#fff200',
        'highlight-hover': '#ffe029'
      },
      screens: {
        'sm': '640px',
        'md': '782px',
        'md-lg': '996px',
        'lg': '1080px',
        'lg-xl': '1185px',
        'xl': '1260px',
        '2xl': '1536px',
      }
    },
    fontFamily: {
      sans: ['Mukta', 'sans-serif'],
      serif: ['Merriweather', 'serif'],
    },
    listStyleType: {
      none: 'none',
      disc: 'disc',
      decimal: 'decimal',
      square: 'square',
      roman: 'upper-roman',
      circle: 'circle'
    },
    fontSize: {
      '2xs': '.65rem',
      'xs': '.75rem',
      'sm': '.875rem',
      'tiny': '.875rem',
      'base': '1rem',
      'lg': '1.125rem',
      'xl': '1.25rem',
      '2xl': '1.5rem',
      '3xl': '1.875rem',
      '4xl': '2.25rem',
      '5xl': '3rem',
      '6xl': '4rem',
      '7xl': '5rem',
    }
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
  ],
};
