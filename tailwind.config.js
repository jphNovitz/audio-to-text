/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
   plugins: [
        require("@tailwindcss/typography"),
        require('daisyui')
    ],
    // darkMode: "media",
    daisyui: {
        themes: ['corporate', 'dracula'],
        darkTheme: "dracula",
    },
  theme: {
    extend: {
      transitionProperty: {
        'spacing': 'margin, padding, display',
        'width': 'width',
      },
      colors: {
        "transparent": "transparent",
        "white": "#E8E8E8",
        "base": {
          "light": "rgb(209 213 219 / var(--tw-bg-opacity, 1))",
          "dark": "#1E1E1E",
        },
        "surface": {
          "light": "rgb(229 231 235 / var(--tw-bg-opacity, 1))",
          "dark": "#1E1E1E",
          "secondary": "#FFC537",
        },
        "content": {
          "primary": {
            "light": "#333333",
            "dark": "#E0E0E0",
          },
          "secondary": "#1E5D9D",
        },
      },
    },
  },
}
