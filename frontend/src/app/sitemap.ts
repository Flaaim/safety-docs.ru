import type { MetadataRoute } from "next";
import { getAllDirections } from "@api/direction";
import { getCategoriesByDirectionSlug } from "@api/category";

export const dynamic = "force-dynamic";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://safety-docs.ru";

  const staticPages: MetadataRoute.Sitemap = [
    { url: `${baseUrl}/`, lastModified: new Date() },
    { url: `${baseUrl}/directions`, lastModified: new Date() },
    { url: `${baseUrl}/success`, lastModified: new Date() },
    { url: `${baseUrl}/terms`, lastModified: new Date() },
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
              changeFrequency: "monthly" as const,
              priority: 0.6,
            };
            const children = (cat.children ?? []).map((child) => ({
              url: `${baseUrl}/directions/${dir.slug}/${child.slug}`,
              lastModified: new Date(),
              changeFrequency: "monthly" as const,
              priority: 0.6,
            }));
            return [self, ...children];
          });
        })
      )
    ).flat();

    return [...staticPages, ...directionUrls, ...categoryUrls];
  } catch (error) {
    console.error("Ошибка при генерации sitemap:", error);
    return staticPages;
  }
}
