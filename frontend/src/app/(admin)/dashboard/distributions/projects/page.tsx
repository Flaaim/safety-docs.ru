import AddNewProjectDialog from "@/components/Admin/Dashboard/Distributions/Projects/add-new-project-dialog";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {cookies} from "next/headers";
import {getAllProjects} from "@api/distribution";
import {FileDTO, ProjectsDTO} from "@/interfaces/distribution.interface";
import AddImportContactsDialog from "@/components/Admin/Dashboard/Distributions/Import/add-import-contacts-dialog";
import DeleteContactsFileDialog from "@/components/Admin/Dashboard/Distributions/Import/delete-contacts-file-dialog";

export default async function ProjectPage() {

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getAllProjects(token);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Проекты</h1>
        <AddNewProjectDialog/>
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>№</TableHead>
              <TableHead>Название</TableHead>
              <TableHead>Количество контактов</TableHead>
              <TableHead>Удалить</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.projects.map((project: ProjectsDTO, idx) => (
              <TableRow key={project.id}>
                <TableCell>{idx + 1}</TableCell>
                <TableCell>{project.name}</TableCell>
                <TableCell>{project.contacts.length}</TableCell>
                <TableCell><DeleteContactsFileDialog fileId={project.id}/></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
