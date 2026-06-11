import AddUploadContactsDialog from "@/components/Admin/Dashboard/Distributions/Import/add-upload-contacts-dialog";
import {cookies} from "next/headers";
import {getContactFiles} from "@api/distribution";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {FileDTO} from "@/interfaces/distribution.interface";
import DeleteContactsFileDialog from "@/components/Admin/Dashboard/Distributions/Import/delete-contacts-file-dialog";
import AddImportContactsDialog from "@/components/Admin/Dashboard/Distributions/Import/add-import-contacts-dialog";

interface FileImportPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}

export default async function ImportPage({ searchParams }: FileImportPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 20;

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getContactFiles(token, currentPage, perPage);

  const hasFiles = data && data.files && data.files.length > 0;

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
              <TableHead >Удалить</TableHead>
            </TableRow>
          </TableHeader>
          {hasFiles ? (
            <TableBody>
              {data.files.map((file: FileDTO, idx) => (
                <TableRow key={file.id}>
                  <TableCell>{idx + 1}</TableCell>
                  <TableCell>{file.name}</TableCell>
                  <TableCell>{file.date}</TableCell>
                  <TableCell>{file.complete ? (<div>Обработан</div>) : (<AddImportContactsDialog fileId={file.id} />)}</TableCell>
                  <TableCell><DeleteContactsFileDialog fileId={file.id}/></TableCell>
                </TableRow>
              ))}
            </TableBody>
          ) : ('')}

        </Table>
      </div>
    </div>
  );
}
