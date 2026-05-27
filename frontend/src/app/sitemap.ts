import type { MetadataRoute } from "next";
import { getAllDirections } from "@api/direction";

export const dynamic = "force-dynamic";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = "https://safety-docs.ru";

  const staticPages: MetadataRoute.Sitemap = [
    { url: `${baseUrl}/`, lastModified: new Date() },
    { url: `${baseUrl}/success`, lastModified: new Date() },
    { url: `${baseUrl}/terms`, lastModified: new Date() },
  ];

  try {
    const data = await getAllDirections();
    const directionUrls: MetadataRoute.Sitemap = data.directions.map((dir) => ({
      url: `${baseUrl}/docs/${dir.slug}`,
      lastModified: new Date(),
      changeFrequency: "weekly",
      priority: 0.8,
    }));

    const categoryUrls: MetadataRoute.Sitemap = data.directions.flatMap((dir) =>
      dir.categories.map((cat) => ({
        url: `${baseUrl}/docs/${dir.slug}/${cat.slug}`,
        lastModified: new Date(),
        changeFrequency: "monthly",
        priority: 0.6,
      }))
    );
    return [...staticPages, ...directionUrls, ...categoryUrls];
  } catch (error) {
    console.error("Ошибка при генерации sitemap:", error);
    return staticPages;
  }
}
