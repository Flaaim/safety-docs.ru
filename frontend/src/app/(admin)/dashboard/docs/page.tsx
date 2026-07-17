import { cookies } from "next/headers";
import Link from "next/link";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import PaginationControls from "@/components/PaginationControls/PaginationControls";
import AdminBreadcrumbs from "@/components/Admin/Dashboard/AdminBreadcrumbs";
import { getAdminTemplates } from "@api/document";

interface DocsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function DocsPage({ searchParams }: DocsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 20;

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getAdminTemplates(token, currentPage, perPage);

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Документы" }]} />

      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Документы</h1>
          <p className="text-sm text-muted-foreground mt-1">
            Сводный список шаблонов. Дерево каталога — в разделе{" "}
            <Link href="/dashboard/directions" className="underline">
              Направления
            </Link>
            .
          </p>
        </div>
      </div>

      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Направление</TableHead>
              <TableHead>Категория</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Создан</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.templates.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground">
                  Документов пока нет.
                </TableCell>
              </TableRow>
            ) : (
              data.templates.map((row) => (
                <TableRow key={row.id}>
                  <TableCell className="font-medium">{row.name}</TableCell>
                  <TableCell>{row.directionName}</TableCell>
                  <TableCell>{row.categoryName}</TableCell>
                  <TableCell className="text-muted-foreground">{row.status}</TableCell>
                  <TableCell className="text-muted-foreground">
                    {new Date(row.createdAt).toLocaleDateString("ru-RU")}
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
        <PaginationControls currentPage={data.currentPage} totalPages={data.totalPages} />
      </div>
    </div>
  );
}
