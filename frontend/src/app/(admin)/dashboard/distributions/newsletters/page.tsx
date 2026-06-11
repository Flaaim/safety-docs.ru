import {cookies} from "next/headers";
import {getAllNewslettersPaginated} from "@api/distribution";
import AddNewProjectDialog from "@/components/Admin/Dashboard/Distributions/Projects/add-new-project-dialog";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {Newsletter} from "@/interfaces/distribution.interface";


export default async function NewslettersPage() {

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getAllNewslettersPaginated(token);

  const hasNewsletters = data && data.newsletters && data.newsletters.length > 0;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Рассылки</h1>

      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>№</TableHead>
              <TableHead>Тема</TableHead>
              <TableHead>Шаблон ID</TableHead>
              <TableHead>Создана</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead >Удалить</TableHead>
            </TableRow>
          </TableHeader>
          {hasNewsletters ? (
            <TableBody>
              {data.newsletters.map((newsletter:Newsletter, idx) => (
                <TableRow key={idx}>
                  <TableCell>{idx + 1}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          ) : ('')}
        </Table>
      </div>
    </div>
  );
}
