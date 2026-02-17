import type { MetadataRoute } from 'next'

export const dynamic = 'force-static'

export default function sitemap(): MetadataRoute.Sitemap {

  const baseUrl = 'https://safety-docs.ru'
  return [
    { url: `${baseUrl}/`, lastModified: new Date() },
    { url: `${baseUrl}/safety`, lastModified: new Date() },
    { url: `${baseUrl}/safety/education`, lastModified: new Date() },
    { url: `${baseUrl}/safety/service`, lastModified: new Date() },
    { url: `${baseUrl}/safety/suot`, lastModified: new Date() },
    { url: `${baseUrl}/success`, lastModified: new Date() },
  ];
}
