import React from "react";
import Link from "next/link";
import { notFound } from "next/navigation";
import { cookies } from "next/headers";
import { ChevronRight } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { getDirectionBySlug } from "@api/direction";
import { getCategoriesByDirectionSlug } from "@api/category";
import { Category } from "@/types/category";
import AddCategoryDialog from "@/components/Admin/Dashboard/Categories/add-category-dialog";
import EditCategoryDialog from "@/components/Admin/Dashboard/Categories/edit-category-dialog";
import DeleteCategoryDialog from "@/components/Admin/Dashboard/Categories/delete-category-dialog";
import AdminBreadcrumbs from "@/components/Admin/Dashboard/AdminBreadcrumbs";

export default async function DirectionCategoriesPage({
  params,
}: {
  params: Promise<{ directionSlug: string }>;
}) {
  const { directionSlug } = await params;
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  let direction;
  try {
    direction = await getDirectionBySlug(directionSlug, token);
  } catch {
    notFound();
  }

  const categories = await getCategoriesByDirectionSlug(directionSlug, token);
  const rootCategories = categories.filter((c) => c.parentId === null);

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs
        items={[
          { title: "Направления", href: "/dashboard/directions" },
          { title: direction.title },
        ]}
      />

      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">{direction.title}</h1>
          <p className="text-sm text-muted-foreground mt-1">Категории направления</p>
        </div>
        <AddCategoryDialog directionId={direction.id} />
      </div>

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
            {rootCategories.length === 0 ? (
              <TableRow>
                <TableCell colSpan={4} className="text-muted-foreground">
                  В этом направлении пока нет категорий.
                </TableCell>
              </TableRow>
            ) : (
              rootCategories.map((cat: Category) => (
                <React.Fragment key={cat.id}>
                  <TableRow className="bg-muted/20">
                    <TableCell className="font-semibold">
                      <Link
                        href={`/dashboard/directions/${directionSlug}/${cat.slug}`}
                        className="inline-flex items-center gap-1 hover:underline"
                      >
                        {cat.title}
                        <ChevronRight className="h-4 w-4 opacity-50" />
                      </Link>
                    </TableCell>
                    <TableCell className="max-w-[360px]">
                      <div className="line-clamp-2 text-sm text-muted-foreground">
                        {cat.description}
                      </div>
                    </TableCell>
                    <TableCell className="text-muted-foreground">{cat.slug}</TableCell>
                    <TableCell className="text-right">
                      <EditCategoryDialog
                        slug={cat.slug}
                        id={cat.id}
                        directionId={cat.directionId}
                      />
                      <DeleteCategoryDialog categoryId={cat.id} directionId={cat.directionId} />
                    </TableCell>
                  </TableRow>

                  {(cat.children ?? []).map((child: Category) => (
                    <TableRow key={child.id}>
                      <TableCell className="font-medium pl-8 relative">
                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/50">
                          ↳
                        </span>
                        <Link
                          href={`/dashboard/directions/${directionSlug}/${child.slug}`}
                          className="hover:underline"
                        >
                          {child.title}
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
                        <DeleteCategoryDialog
                          categoryId={child.id}
                          directionId={child.directionId}
                        />
                      </TableCell>
                    </TableRow>
                  ))}
                </React.Fragment>
              ))
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
