/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.html"],
  darkMode: "class",
  theme: {
    extend: {
      "colors": {
              "on-primary-fixed": "#171b2b",
              "tertiary-fixed-dim": "#fabd00",
              "inverse-on-surface": "#f0f1f3",
              "outline-variant": "#c7c6cd",
              "on-tertiary-fixed": "#261a00",
              "secondary-fixed": "#4E6700",
              "on-primary-container": "#9599ad",
              "primary-fixed-dim": "#c2c5db",
              "on-error": "#ffffff",
              "surface-container-lowest": "#ffffff",
              "tertiary-container": "#412f00",
              "on-background": "#191c1e",
              "surface": "#f8f9fb",
              "background": "#f8f9fb",
              "on-tertiary-fixed-variant": "#5b4300",
              "on-primary": "#ffffff",
              "on-secondary": "#ffffff",
              "error-container": "#ffdad6",
              "outline": "#77767d",
              "secondary-container": "#4E6700",
              "secondary-fixed-dim": "#3d5200",
              "surface-tint": "#5a5d70",
              "surface-bright": "#f8f9fb",
              "on-secondary-fixed": "#ffffff",
              "surface-container": "#edeef0",
              "tertiary-fixed": "#ffdf9e",
              "on-secondary-fixed-variant": "#3a4d00",
              "surface-variant": "#e1e2e4",
              "on-error-container": "#93000a",
              "inverse-primary": "#c2c5db",
              "on-surface": "#191c1e",
              "secondary": "#4e6700",
              "on-tertiary-container": "#c39200",
              "inverse-surface": "#2e3132",
              "surface-container-highest": "#e1e2e4",
              "on-surface-variant": "#46464c",
              "primary-container": "#2d3142",
              "primary-fixed": "#dee1f8",
              "primary": "#181c2c",
              "tertiary": "#271b00",
              "on-tertiary": "#ffffff",
              "on-primary-fixed-variant": "#424658",
              "error": "#ba1a1a",
              "on-secondary-container": "#536d00",
              "surface-container-high": "#e7e8ea",
              "surface-container-low": "#f3f4f6",
              "surface-dim": "#d9dadc"
      },
      "borderRadius": {
              "DEFAULT": "0.125rem",
              "lg": "0.25rem",
              "xl": "0.5rem",
              "full": "0.75rem"
      },
      "spacing": {
              "base": "8px",
              "max-width": "1280px",
              "margin-mobile": "16px",
              "margin-desktop": "64px",
              "gutter": "24px"
      },
      "fontFamily": {
              "headline-md": [
                      "Montserrat"
              ],
              "headline-lg-mobile": [
                      "Montserrat"
              ],
              "label-sm": [
                      "Inter"
              ],
              "body-lg": [
                      "Inter"
              ],
              "headline-lg": [
                      "Montserrat"
              ],
              "display-lg": [
                      "Montserrat"
              ],
              "body-md": [
                      "Inter"
              ]
      },
      "fontSize": {
              "headline-md": [
                      "24px",
                      {
                              "lineHeight": "32px",
                              "fontWeight": "600"
                      }
              ],
              "headline-lg-mobile": [
                      "28px",
                      {
                              "lineHeight": "36px",
                              "fontWeight": "700"
                      }
              ],
              "label-sm": [
                      "12px",
                      {
                              "lineHeight": "16px",
                              "letterSpacing": "0.05em",
                              "fontWeight": "600"
                      }
              ],
              "body-lg": [
                      "18px",
                      {
                              "lineHeight": "28px",
                              "fontWeight": "400"
                      }
              ],
              "headline-lg": [
                      "32px",
                      {
                              "lineHeight": "40px",
                              "fontWeight": "700"
                      }
              ],
              "display-lg": [
                      "48px",
                      {
                              "lineHeight": "56px",
                              "letterSpacing": "-0.02em",
                              "fontWeight": "700"
                      }
              ],
              "body-md": [
                      "16px",
                      {
                              "lineHeight": "24px",
                              "fontWeight": "400"
                      }
              ]
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/container-queries')
  ]
}
