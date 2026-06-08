import AddUploadContactsDialog from "@/components/Admin/Dashboard/Distributions/Import/add-upload-contacts-dialog";
import {cookies} from "next/headers";
import {getContactFiles} from "@api/distribution";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {FileDTO} from "@/interfaces/distribution.interface";
import {Button} from "@/components/ui/button";
import {Import} from "lucide-react";

interface FileImportPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function ImportPage({ searchParams }: FileImportPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 20;

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getContactFiles(token, currentPage, perPage);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Импорт контактов</h1>
        <AddUploadContactsDialog/>
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>№</TableHead>
              <TableHead>Имя файла</TableHead>
              <TableHead>Загружен</TableHead>
              <TableHead>Импорт</TableHead>
              <TableHead className="text-right">Удалить</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.files.map((file: FileDTO, idx) => (
              <TableRow key={file.id}>
                <TableCell>{idx + 1}</TableCell>
                <TableCell>{file.name}</TableCell>
                <TableCell>{file.date}</TableCell>
                <TableCell>{file.complete ? (<div>Обработан</div>) : (<Button><Import /></Button>)}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
