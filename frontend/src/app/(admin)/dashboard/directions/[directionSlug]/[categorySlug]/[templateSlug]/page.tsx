import { notFound } from "next/navigation";
import { cookies } from "next/headers";
import { getDirectionBySlug } from "@api/direction";
import { getCategoryBySlugs } from "@api/category";
import { getTemplateBySlugs } from "@api/document";
import AdminBreadcrumbs from "@/components/Admin/Dashboard/AdminBreadcrumbs";

const formatPrice = (amount: number) =>
  new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    maximumFractionDigits: 0,
  }).format(amount);

export default async function TemplateAdminPage({
  params,
}: {
  params: Promise<{
    directionSlug: string;
    categorySlug: string;
    templateSlug: string;
  }>;
}) {
  const { directionSlug, categorySlug, templateSlug } = await params;
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  let direction;
  try {
    direction = await getDirectionBySlug(directionSlug, token);
  } catch {
    notFound();
  }

  let category;
  try {
    category = await getCategoryBySlugs(directionSlug, categorySlug, token);
  } catch {
    notFound();
  }

  let template;
  try {
    template = await getTemplateBySlugs(directionSlug, categorySlug, templateSlug, token);
  } catch {
    notFound();
  }

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs
        items={[
          { title: "Направления", href: "/dashboard/directions" },
          { title: direction.title, href: `/dashboard/directions/${directionSlug}` },
          {
            title: category.title,
            href: `/dashboard/directions/${directionSlug}/${categorySlug}`,
          },
          { title: template.name },
        ]}
      />

      <div>
        <h1 className="text-3xl font-bold">{template.name}</h1>
        <p className="text-sm text-muted-foreground mt-1">Карточка документа (шаблона)</p>
      </div>

      <dl className="grid gap-4 sm:grid-cols-2 max-w-3xl">
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">ID</dt>
          <dd className="mt-1 font-mono text-sm break-all">{template.id}</dd>
        </div>
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">Стоимость</dt>
          <dd className="mt-1 text-lg font-semibold">{formatPrice(template.amount)}</dd>
        </div>
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">Файл</dt>
          <dd className="mt-1 break-all">{template.filename}</dd>
        </div>
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">Slug</dt>
          <dd className="mt-1 font-mono text-sm">{template.slug}</dd>
        </div>
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">Создан</dt>
          <dd className="mt-1">{new Date(template.createdAt).toLocaleString("ru-RU")}</dd>
        </div>
        <div className="rounded-lg border bg-white p-4">
          <dt className="text-sm text-muted-foreground">Категория</dt>
          <dd className="mt-1">{category.title}</dd>
        </div>
      </dl>
    </div>
  );
}
