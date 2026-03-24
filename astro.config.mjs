// @ts-check
import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';
import mdx from '@astrojs/mdx';
import sitemap from '@astrojs/sitemap';

// https://astro.build/config
export default defineConfig({
  site: 'https://jolero.eu',
  integrations: [tailwind(), mdx(), sitemap()],
  i18n: {
    defaultLocale: 'en',
    locales: ['en', 'hu', 'ro'],
    routing: {
      prefixDefaultLocale: false,
      redirectToDefaultLocale: false,
    },
  },
});
