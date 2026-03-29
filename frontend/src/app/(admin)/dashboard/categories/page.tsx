import {cookies} from "next/headers";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {getAllCategories} from "@api/category";
import {CategoryDTO} from "@/interfaces/category.interface";
import AddCategoryDialog from "@/components/Admin/Dashboard/Categories/add-category-dialog";
import EditCategoryDialog from "@/components/Admin/Dashboard/Categories/edit-category-dialog";



export default async function CategoriesPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data  = await getAllCategories(token)

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
              <TableHead>Описание</TableHead>
              <TableHead>Направление</TableHead>
              <TableHead>slug</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.categories.map((cat: CategoryDTO) => (
              <TableRow key={cat.slug}>
                <TableCell className="font-medium">{cat.title}</TableCell>
                <TableCell className="max-w-[400px] font-medium">
                  <div className="line-clamp-2 text-sm text-muted-foreground">
                    {cat.description}
                  </div>
                </TableCell>
                <TableCell className="font-medium">{cat.directionTitle}</TableCell>
                <TableCell className="text-muted-foreground">{cat.slug}</TableCell>
                <TableCell className="text-right">
                  {<EditCategoryDialog slug={cat.slug} id={cat.id} directionId={cat.directionId}/>}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

    </div>
  )
}
