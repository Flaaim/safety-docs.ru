import { notFound } from "next/navigation";
import Link from "next/link";
import { cache } from "react";
import { Metadata } from "next";
import { ChevronRight, FileText } from "lucide-react";
import { Htag, Ptag } from "@/components";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown from "@/utils/normalizeMarkdown";
import SiteBreadcrumbs from "@/components/Breadcrumb/SiteBreadcrumbs";
import { getAllDirections, getDirectionBySlug } from "@api/direction";
import { getCategoriesByDirectionSlug, getCategoryBySlugs } from "@api/category";
import { getTemplatesByCategorySlugs } from "@api/document";

const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});

export const dynamicParams = true;

export async function generateStaticParams() {
  try {
    const directions = await getAllDirections();
    const paths: { directionSlug: string; categorySlug: string }[] = [];

    for (const dir of directions) {
      const categories = await getCategoriesByDirectionSlug(dir.slug);
      for (const cat of categories) {
        paths.push({ directionSlug: dir.slug, categorySlug: cat.slug });
        for (const child of cat.children ?? []) {
          paths.push({ directionSlug: dir.slug, categorySlug: child.slug });
        }
      }
    }

    return paths;
  } catch {
    return [];
  }
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ directionSlug: string; categorySlug: string }>;
}): Promise<Metadata> {
  const { directionSlug, categorySlug } = await params;

  try {
    const category = await getCategoryBySlugs(directionSlug, categorySlug);

    return {
      title: category.title,
      description: category.description,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных категории ${categorySlug}:`, error);
    return {
      title: "Категория не найдена",
      description: "Запрашиваемая категория не существует.",
    };
  }
}

export default async function CategoryTemplatesPage({
  params,
}: {
  params: Promise<{ directionSlug: string; categorySlug: string }>;
}) {
  const { directionSlug, categorySlug } = await params;

  let direction;
  try {
    direction = await getCachedDirection(directionSlug);
  } catch {
    notFound();
  }

  let category;
  try {
    category = await getCategoryBySlugs(directionSlug, categorySlug);
  } catch {
    notFound();
  }

  const children = category.children ?? [];
  const templates =
    children.length === 0 ? await getTemplatesByCategorySlugs(directionSlug, categorySlug) : [];

  const formatPrice = (amount: number) =>
    new Intl.NumberFormat("ru-RU", {
      style: "currency",
      currency: "RUB",
      maximumFractionDigits: 0,
    }).format(amount);

  return (
    <>
      <SiteBreadcrumbs
        items={[
          { title: "Направления", href: "/directions" },
          { title: direction.title, href: `/directions/${directionSlug}` },
          { title: category.title },
        ]}
      />

      <Htag tag="h1">{category.title}</Htag>
      {category.description && (
        <Ptag className="text-muted-foreground">{category.description}</Ptag>
      )}

      {children.length > 0 ? (
        <div className="grid gap-4 sm:grid-cols-2 mt-6">
          {children.map((child) => (
            <Link
              key={child.id}
              href={`/directions/${directionSlug}/${child.slug}`}
              className="flex items-center gap-3 p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
            >
              <FileText className="text-primary/70 shrink-0" size={28} />
              <span className="font-medium flex-1">{child.title}</span>
              <ChevronRight className="h-4 w-4 opacity-50" />
            </Link>
          ))}
        </div>
      ) : (
        <div className="mt-6 space-y-4">
          {category.text && (
            <div className="prose prose-slate max-w-none border-b pb-6">
              <MarkdownRenderer content={normalizeMarkdown(category.text)} />
            </div>
          )}

          <Htag tag="h2">Документы</Htag>
          {templates.length === 0 ? (
            <Ptag>В этой категории пока нет документов.</Ptag>
          ) : (
            <div className="grid gap-3">
              {templates.map((template) => (
                <Link
                  key={template.id}
                  href={`/directions/${directionSlug}/${categorySlug}/${template.slug}`}
                  className="flex items-center justify-between gap-4 p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
                >
                  <div className="min-w-0">
                    <div className="font-medium truncate">{template.name}</div>
                    <div className="text-sm text-muted-foreground">{template.filename}</div>
                  </div>
                  <div className="shrink-0 font-semibold tabular-nums">
                    {formatPrice(template.amount)}
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      )}
    </>
  );
}
