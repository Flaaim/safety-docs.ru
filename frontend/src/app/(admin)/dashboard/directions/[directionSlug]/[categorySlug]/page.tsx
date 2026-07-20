import Link from "next/link";
import { notFound } from "next/navigation";
import { cookies } from "next/headers";
import { ChevronRight, FileText } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { getDirectionBySlug } from "@api/direction";
import { getCategoriesByDirectionSlug, getCategoryBySlugs } from "@api/category";
import { getTemplatesByCategorySlugs, EMPTY_PAGINATED_TEMPLATES } from "@api/document";
import AddCategoryDialog from "@/components/Admin/Dashboard/Categories/add-category-dialog";
import EditCategoryDialog from "@/components/Admin/Dashboard/Categories/edit-category-dialog";
import DeleteCategoryDialog from "@/components/Admin/Dashboard/Categories/delete-category-dialog";
import BulkUploadDocuments from "@/components/Admin/Dashboard/Documents/bulk-upload-documents";
import AdminBreadcrumbs from "@/components/Admin/Dashboard/AdminBreadcrumbs";

const formatPrice = (amount: number) =>
  new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    maximumFractionDigits: 0,
  }).format(amount);

export default async function CategoryNestedPage({
  params,
}: {
  params: Promise<{ directionSlug: string; categorySlug: string }>;
}) {
  const { directionSlug, categorySlug } = await params;
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

  const children = category.children ?? [];
  const allCategories = await getCategoriesByDirectionSlug(directionSlug, token);
  const templatesData =
    children.length === 0
      ? await getTemplatesByCategorySlugs(directionSlug, categorySlug, token)
      : EMPTY_PAGINATED_TEMPLATES;

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs
        items={[
          { title: "Направления", href: "/dashboard/directions" },
          { title: direction.title, href: `/dashboard/directions/${directionSlug}` },
          { title: category.title },
        ]}
      />

      <div className="flex items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold">{category.title}</h1>
          <p className="text-sm text-muted-foreground mt-1">
            {children.length > 0 ? "Подкатегории" : "Документы (шаблоны) категории"}
          </p>
        </div>
        <div className="flex items-center gap-2">
          {children.length > 0 && (
            <AddCategoryDialog directionId={direction.id} defaultParentId={category.id} />
          )}
          <EditCategoryDialog
            slug={category.slug}
            id={category.id}
            directionId={category.directionId}
          />
          <DeleteCategoryDialog categoryId={category.id} directionId={category.directionId} />
        </div>
      </div>

      <BulkUploadDocuments
        directionId={direction.id}
        categories={allCategories}
        defaultCategoryId={category.id}
      />

      {children.length > 0 ? (
        <div className="rounded-md border bg-white">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Название</TableHead>
                <TableHead>Описание</TableHead>
                <TableHead>slug</TableHead>
                <TableHead className="text-right">Действия</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {children.map((child) => (
                <TableRow key={child.id}>
                  <TableCell className="font-medium">
                    <Link
                      href={`/dashboard/directions/${directionSlug}/${child.slug}`}
                      className="inline-flex items-center gap-1 hover:underline"
                    >
                      <FileText className="h-4 w-4 text-muted-foreground" />
                      {child.title}
                      <ChevronRight className="h-4 w-4 opacity-50" />
                    </Link>
                  </TableCell>
                  <TableCell className="max-w-[360px]">
                    <div className="line-clamp-2 text-sm text-muted-foreground">
                      {child.description}
                    </div>
                  </TableCell>
                  <TableCell className="text-muted-foreground">{child.slug}</TableCell>
                  <TableCell className="text-right">
                    <EditCategoryDialog
                      slug={child.slug}
                      id={child.id}
                      directionId={child.directionId}
                    />
                    <DeleteCategoryDialog categoryId={child.id} directionId={child.directionId} />
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      ) : (
        <div className="rounded-md border bg-white">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Название</TableHead>
                <TableHead>Цена</TableHead>
                <TableHead>Файл</TableHead>
                <TableHead>slug</TableHead>
                <TableHead>Создан</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {templatesData.items.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={5} className="text-muted-foreground">
                    В этой категории пока нет документов.
                  </TableCell>
                </TableRow>
              ) : (
                templatesData.items.map((template) => (
                  <TableRow key={template.id}>
                    <TableCell className="font-medium">
                      <Link
                        href={`/dashboard/directions/${directionSlug}/${categorySlug}/${template.slug}`}
                        className="hover:underline"
                      >
                        {template.name}
                      </Link>
                    </TableCell>
                    <TableCell>{formatPrice(template.amount)}</TableCell>
                    <TableCell className="text-muted-foreground break-all">
                      {template.filename}
                    </TableCell>
                    <TableCell className="text-muted-foreground">{template.slug}</TableCell>
                    <TableCell className="text-muted-foreground">
                      {new Date(template.createdAt).toLocaleDateString("ru-RU")}
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}
