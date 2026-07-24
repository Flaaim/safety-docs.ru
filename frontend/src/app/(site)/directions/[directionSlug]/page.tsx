import { notFound } from "next/navigation";
import { Htag, Ptag } from "@/components";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown from "@/utils/normalizeMarkdown";
import Link from "next/link";
import { ChevronRight } from "lucide-react";
import { getAllDirections, getDirectionBySlug } from "@api/direction";
import { getCategoriesByDirectionSlug } from "@api/category";
import { cache } from "react";
import { Metadata } from "next";
import SiteBreadcrumbs from "@/components/Breadcrumb/SiteBreadcrumbs";

const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});

export const dynamic = 'force-dynamic';

export async function generateStaticParams() {
  try {
    const directions = await getAllDirections();
    return directions.map((dir) => ({ directionSlug: dir.slug }));
  } catch (error) {
    console.error("Ошибка при получении статических путей направлений:", error);
    return [];
  }
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ directionSlug: string }>;
}): Promise<Metadata> {
  const { directionSlug } = await params;

  try {
    const direction = await getCachedDirection(directionSlug);

    return {
      title: direction.title,
      description: direction.description,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных направления ${directionSlug}:`, error);
    return {
      title: "Направление не найдено",
      description: "Запрашиваемое направление не существует.",
    };
  }
}

export default async function DirectionCategoriesPage({
  params,
}: {
  params: Promise<{ directionSlug: string }>;
}) {
  const { directionSlug } = await params;

  let direction;
  try {
    direction = await getCachedDirection(directionSlug);
  } catch (error) {
    console.error(`Ошибка API для направления ${directionSlug}:`, error);
    notFound();
  }

  const categories = await getCategoriesByDirectionSlug(directionSlug);
  const rootCategories = categories.filter((c) => c.parentId === null);

  return (
    <>
      <SiteBreadcrumbs
        items={[{ title: "Направления", href: "/directions" }, { title: direction.title }]}
      />
      <Htag tag="h1">{direction.title}</Htag>
      {direction.text && <MarkdownRenderer content={normalizeMarkdown(direction.text)} />}
      <Htag tag="h2">Категории</Htag>
      {rootCategories.length === 0 ? (
        <Ptag>В этом направлении пока нет категорий.</Ptag>
      ) : (
        <div className="grid gap-3 sm:grid-cols-2 mt-6">
          {rootCategories.map((category) => (
            <Link
              key={category.id}
              href={`/directions/${directionSlug}/${category.slug}`}
              className="flex items-center justify-between p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
            >
              <span className="font-medium">{category.title}</span>
              <ChevronRight className="h-4 w-4 opacity-50" />
            </Link>
          ))}
        </div>
      )}
    </>
  );
}
