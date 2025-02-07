/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      transitionProperty: {
        'spacing': 'margin, padding, display',
        'width': 'width',
      },
      colors: {
        "transparent": "transparent",
        "white": "#F8F9FA",
        "base": {
          "light": "#FFFFFF", // Blanc pur pour le fond en mode clair
          "dark": "#121212",  // Noir profond pour le fond en mode sombre
        },
        "surface": {
          "light": "#F5F5F5",
          "dark": "#1E1E1E",
          "secondary": "#B0B0B0",
        },
        "content": {
          "primary": {
            "light": "#333333",
            "dark": "#E0E0E0",
          },
          "secondary": "#8C8C8C",
        },
      },
    },
  },
  plugins: [],
}
