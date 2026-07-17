import { notFound } from "next/navigation";
import { cache } from "react";
import { Metadata } from "next";
import { Htag, ProductForm } from "@/components";
import SiteBreadcrumbs from "@/components/Breadcrumb/SiteBreadcrumbs";
import { getAllDirections, getDirectionBySlug } from "@api/direction";
import { getCategoriesByDirectionSlug, getCategoryBySlugs } from "@api/category";
import { getTemplateBySlugs, getTemplatesByCategorySlugs } from "@api/document";

const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});

export const dynamicParams = true;

export async function generateStaticParams() {
  try {
    const directions = await getAllDirections();
    const paths: {
      directionSlug: string;
      categorySlug: string;
      templateSlug: string;
    }[] = [];

    for (const dir of directions) {
      const categories = await getCategoriesByDirectionSlug(dir.slug);
      const leafCategories = categories.flatMap((cat) =>
        (cat.children?.length ?? 0) > 0 ? cat.children : [cat]
      );

      for (const cat of leafCategories) {
        const templates = await getTemplatesByCategorySlugs(dir.slug, cat.slug);
        for (const template of templates) {
          paths.push({
            directionSlug: dir.slug,
            categorySlug: cat.slug,
            templateSlug: template.slug,
          });
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
  params: Promise<{
    directionSlug: string;
    categorySlug: string;
    templateSlug: string;
  }>;
}): Promise<Metadata> {
  const { directionSlug, categorySlug, templateSlug } = await params;

  try {
    const [category, template] = await Promise.all([
      getCategoryBySlugs(directionSlug, categorySlug),
      getTemplateBySlugs(directionSlug, categorySlug, templateSlug),
    ]);

    return {
      title: template.name,
      description: `Купить документ «${template.name}» в категории «${category.title}»`,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных документа ${templateSlug}:`, error);
    return {
      title: "Документ не найден",
      description: "Запрашиваемый документ не существует.",
    };
  }
}

export default async function TemplatePurchasePage({
  params,
}: {
  params: Promise<{
    directionSlug: string;
    categorySlug: string;
    templateSlug: string;
  }>;
}) {
  const { directionSlug, categorySlug, templateSlug } = await params;

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

  let template;
  try {
    template = await getTemplateBySlugs(directionSlug, categorySlug, templateSlug);
  } catch {
    notFound();
  }

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
          {
            title: category.title,
            href: `/directions/${directionSlug}/${categorySlug}`,
          },
          { title: template.name },
        ]}
      />

      <Htag tag="h1">{template.name}</Htag>

      <dl className="mt-6 grid gap-3 sm:grid-cols-2 text-sm">
        <div className="rounded-lg border p-4">
          <dt className="text-muted-foreground">Стоимость</dt>
          <dd className="mt-1 text-lg font-semibold">{formatPrice(template.amount)}</dd>
        </div>
        <div className="rounded-lg border p-4">
          <dt className="text-muted-foreground">Файл</dt>
          <dd className="mt-1 font-medium break-all">{template.filename}</dd>
        </div>
        <div className="rounded-lg border p-4">
          <dt className="text-muted-foreground">Обновлён</dt>
          <dd className="mt-1 font-medium">
            {new Date(template.createdAt).toLocaleDateString("ru-RU")}
          </dd>
        </div>
        <div className="rounded-lg border p-4">
          <dt className="text-muted-foreground">Категория</dt>
          <dd className="mt-1 font-medium">{category.title}</dd>
        </div>
      </dl>

      <section className="mt-10 max-w-md rounded-lg border p-6 bg-card">
        <Htag tag="h2">Оформление покупки</Htag>
        <p className="text-sm text-muted-foreground mb-4">
          Укажите email — на него придёт ссылка на скачивание после оплаты.
        </p>
        <ProductForm documentId={template.id} showTerms />
      </section>
    </>
  );
}
