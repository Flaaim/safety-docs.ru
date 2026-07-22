import type { MetadataRoute } from "next";
import { getAllDirections } from "@api/direction";
import { getCategoriesByDirectionSlug } from "@api/category";
import { getSitemapDocuments } from "@api/sitemap";

export const dynamic = "force-dynamic";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://safety-docs.ru";

  const staticPages: MetadataRoute.Sitemap = [
    { url: `${baseUrl}/`, lastModified: new Date(), changeFrequency: "daily", priority: 1.0 },
    {
      url: `${baseUrl}/directions`,
      lastModified: new Date(),
      changeFrequency: "weekly",
      priority: 0.8,
    },
    {
      url: `${baseUrl}/success`,
      lastModified: new Date(),
      changeFrequency: "yearly",
      priority: 0.1,
    },
    { url: `${baseUrl}/terms`, lastModified: new Date(), changeFrequency: "yearly", priority: 0.2 },
  ];

  try {
    const directions = await getAllDirections();
    const directionUrls: MetadataRoute.Sitemap = directions.map((dir) => ({
      url: `${baseUrl}/directions/${dir.slug}`,
      lastModified: new Date(),
      changeFrequency: "weekly",
      priority: 0.8,
    }));

    const categoryUrls: MetadataRoute.Sitemap = (
      await Promise.all(
        directions.map(async (dir) => {
          const categories = await getCategoriesByDirectionSlug(dir.slug);
          return categories.flatMap((cat) => {
            const self = {
              url: `${baseUrl}/directions/${dir.slug}/${cat.slug}`,
              lastModified: new Date(),
              changeFrequency: "weekly" as const,
              priority: 0.6,
            };
            const children = (cat.children ?? []).map((child) => ({
              url: `${baseUrl}/directions/${dir.slug}/${child.slug}`,
              lastModified: new Date(),
              changeFrequency: "weekly" as const,
              priority: 0.6,
            }));
            return [self, ...children];
          });
        })
      )
    ).flat();

    const sitemapDocs = await getSitemapDocuments();
    const documentUrls: MetadataRoute.Sitemap = sitemapDocs.map((doc) => ({
      url: `${baseUrl}/directions/${doc.directionSlug}/${doc.categorySlug}/${doc.documentSlug}`,
      lastModified: doc.createdAt ? new Date(doc.createdAt) : new Date(),
      changeFrequency: "monthly" as const,
      priority: 0.5,
    }));

    return [...staticPages, ...directionUrls, ...categoryUrls, ...documentUrls];
  } catch (error) {
    console.error("Ошибка при генерации sitemap:", error);
    return staticPages;
  }
}
