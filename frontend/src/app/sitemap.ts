import type { MetadataRoute } from 'next';

export const dynamic = 'force-static';

export default function sitemap(): MetadataRoute.Sitemap {

  const baseUrl = 'https://safety-docs.ru';
  return [
    { url: `${baseUrl}/`, lastModified: new Date() },
    { url: `${baseUrl}/docs/safety`, lastModified: new Date() },
    { url: `${baseUrl}/docs/energy`, lastModified: new Date() },
    { url: `${baseUrl}/docs/safety/syot-complect-documentov`, lastModified: new Date() },
    { url: `${baseUrl}/docs/safety/safety-education-complect-documentov`, lastModified: new Date() },
    { url: `${baseUrl}/docs/safety/sluzhba-ohrany-truda-komplekt-dokumentov`, lastModified: new Date() },
    { url: `${baseUrl}/docs/safety/iot-and-pravila-po-ohrane-truda-complect`, lastModified: new Date() },
    { url: `${baseUrl}/docs/energy/15-prikazov-po-electrobezopastnosti`, lastModified: new Date() },
    { url: `${baseUrl}/docs/energy/13-zhurnalov-otvetstvennogo-za-elektrohozyajstvo`, lastModified: new Date() },
    { url: `${baseUrl}/docs/industrial/boiler-room-dogovor-arendy-complect`, lastModified: new Date() },
    { url: `${baseUrl}/docs/industrial/blasting-operations-set-documents`, lastModified: new Date() },
    { url: `${baseUrl}/docs/industrial/kran-dogovor-arendy-complect`, lastModified: new Date() },
    { url: `${baseUrl}/success`, lastModified: new Date() },
  ];
}
