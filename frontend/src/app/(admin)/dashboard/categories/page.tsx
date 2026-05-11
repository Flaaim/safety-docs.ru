import React from "react";
import {cookies} from "next/headers";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {getAllCategories} from "@api/category";
import {CategoryDTO} from "@/interfaces/category.interface";
import AddCategoryDialog from "@/components/Admin/Dashboard/Categories/add-category-dialog";
import EditCategoryDialog from "@/components/Admin/Dashboard/Categories/edit-category-dialog";
import AssignProductDialog from "@/components/Admin/Dashboard/Categories/assign-product-dialog";
import RefuseProductDialog from "@/components/Admin/Dashboard/Categories/refuse-product-dialog";

export default async function CategoriesPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data  = await getAllCategories(token);
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Категории</h1>
        <AddCategoryDialog />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Направление</TableHead>
              <TableHead>Продукт</TableHead>
              <TableHead>slug</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.categories.map((cat: CategoryDTO) => (
              <React.Fragment key={cat.id}>
                {/* === РОДИТЕЛЬСКАЯ КАТЕГОРИЯ === */}
                <TableRow className="bg-muted/20"> {/* Легкий фон для выделения корня */}
                  <TableCell className="font-semibold">{cat.title}</TableCell>
                  <TableCell className="whitespace-normal break-words font-medium">{cat.directionTitle}</TableCell>
                  <TableCell className="whitespace-normal break-words font-medium"></TableCell>
                  <TableCell className="text-muted-foreground">{cat.slug}</TableCell>
                  <TableCell className="text-right">
                    <EditCategoryDialog slug={cat.slug} id={cat.id} directionId={cat.directionId}/>
                  </TableCell>
                </TableRow>
                {/* === ДОЧЕРНИЕ КАТЕГОРИИ === */}
                {cat.children && cat.children.length > 0 && cat.children.map((child: CategoryDTO) => (
                  <TableRow key={child.id}>
                    {/* Визуальный отступ и стрелочка для подкатегории */}
                    <TableCell className="font-medium pl-8 relative">
                      <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/50">
                        ↳
                      </span>
                      {child.title}
                    </TableCell>
                    <TableCell className="whitespace-normal break-words text-muted-foreground">
                      {child.directionTitle}
                    </TableCell>
                    <TableCell className="whitespace-normal break-words font-medium">
                      {child.productId ? (
                        <div className="flex items-center gap-2">
                          {child.productTitle}
                          <RefuseProductDialog categoryId={child.id} />
                        </div>
                      ) : (
                        <AssignProductDialog categoryId={child.id} />
                      )}
                    </TableCell>
                    <TableCell className="text-muted-foreground">{child.slug}</TableCell>
                    <TableCell className="text-right">
                      <EditCategoryDialog slug={child.slug} id={child.id} directionId={child.directionId}/>
                    </TableCell>
                  </TableRow>
                ))}
              </React.Fragment>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
