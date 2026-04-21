import { notFound } from "next/navigation";
import { Htag } from "@/components";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown from "@/utils/normalizeMarkdown";
import Link from "next/link";
import { ChevronRight } from "lucide-react";
import { getAllDirections, getDirectionBySlug } from "@api/direction";
import { cache } from 'react';

const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});

export const dynamicParams = true;

export async function generateStaticParams() {
  try {
    const data = await getAllDirections();
    return data.directions.map((dir) => ({ dirSlug: dir.slug }));
  } catch(error) {
    console.error("ОШИБКА ПРИ ПОЛУЧЕНИИ СТАТИЧЕСКИХ ПУТЕЙ:", error);
    return [];
  }
}

export default async function DirectionPage({ params }: { params: Promise<{ dirSlug: string }> }) {
  const { dirSlug } = await params;

  let direction;
  try {
    direction = await getCachedDirection(dirSlug);
  } catch(error) {
    console.error(`ОШИБКА API ДЛЯ НАПРАВЛЕНИЯ ${dirSlug}:`, error);
    notFound();
  }

  if (!direction) notFound();

  return (
    <>
      <Link href={`/`} className="text-sm text-muted-foreground hover:underline mb-4 block">
        ← Назад
      </Link>
      <Htag tag='h1'>{direction.title}</Htag>
      <MarkdownRenderer content={normalizeMarkdown(direction.text)} />
      <Htag tag='h2'>Разделы:</Htag>
      <div className="grid gap-3 sm:grid-cols-2 mt-6">
        {direction.categories.map((category) => (
          <Link
            key={category.slug}
            href={`/docs/${direction.slug}/${category.slug}`}
            className="flex items-center justify-between p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
          >
            <span className="font-medium">{category.title}</span>
            <ChevronRight className="h-4 w-4 opacity-50" />
          </Link>
        ))}
      </div>
    </>
  );
}
