import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {getAllDirections} from "@api/direction";
import AddDirectionDialog from "@/components/Admin/Dashboard/Directions/add-direction-dialog";
import EditDirectionDialog from "@/components/Admin/Dashboard/Directions/edit-direction-dialog";
import {cookies} from "next/headers";
import {DirectionDTO} from "@/interfaces/direction.interface";

export default async function DirectionsPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data  = await getAllDirections(token);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Направления</h1>
        <AddDirectionDialog />
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Описание</TableHead>
              <TableHead>Категории</TableHead>
              <TableHead>slug</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.directions.map((dir: DirectionDTO) => (
              <TableRow key={dir.slug}>
                <TableCell className="font-medium">{dir.title}</TableCell>
                <TableCell className="max-w-[400px] font-medium">
                  <div className="line-clamp-2 text-sm text-muted-foreground">
                    {dir.description}
                </div>
                </TableCell>
                <TableCell className="text-muted-foreground">{dir.categories.length}</TableCell>
                <TableCell className="text-muted-foreground">{dir.slug}</TableCell>
                <TableCell className="text-right">
                  <EditDirectionDialog slug={dir.slug} id={dir.id}/>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

    </div>
  );
}
