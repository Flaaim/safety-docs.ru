import {cookies} from "next/headers";
import AddDirectionDialog from "@/components/Admin/Dashboard/Directions/add-direction-dialog";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import EditDirectionDialog from "@/components/Admin/Dashboard/Directions/edit-direction-dialog";


export default async function CategoriesPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  //const data  = await getAllCategories(token)

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Категории</h1>
       {/*// AddCategoriesDialog*/}
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

          </TableBody>
        </Table>
      </div>

    </div>
  )
}
