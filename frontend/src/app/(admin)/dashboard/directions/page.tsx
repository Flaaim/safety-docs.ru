import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Plus, Edit } from "lucide-react";
import Link from "next/link";
import {getAllDirections} from "@api/direction";
import AddDirectionDialog from "@/components/Admin/Dashboard/Directions/add-direction-dialog";

export default async function DirectionsPage() {
  const data  = await getAllDirections()

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
              <TableHead>slug</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.directions.map((dir: any) => (
              <TableRow key={dir.slug}>
                <TableCell className="font-medium">{dir.title}</TableCell>
                <TableCell className="max-w-[400px] font-medium">
                  <div className="line-clamp-2 text-sm text-muted-foreground">
                    {dir.description}
                </div>
                </TableCell>
                <TableCell className="text-muted-foreground">{dir.slug}</TableCell>
                <TableCell className="text-right">
                  <Button variant="ghost" size="icon" asChild>
                    <Link href={`/dashboard/directions/edit/${dir.slug}`}>
                      <Edit className="h-4 w-4" />
                    </Link>
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

    </div>
  )
}
