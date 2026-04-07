import type { Config } from 'tailwindcss'

export default {
  theme: {
    extend: {
      colors: {
        dark: {
          DEFAULT: '#0d1117',
          light: '#161b22',
          lighter: '#1c2333',
          border: '#30363d',
        },
        accent: {
          DEFAULT: '#f0b90b',
          hover: '#d4a50a',
        },
        muted: '#8b949e',
      },
    },
  },
} satisfies Config
