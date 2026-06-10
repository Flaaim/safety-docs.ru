import AddNewProjectDialog from "@/components/Admin/Dashboard/Distributions/Projects/add-new-project-dialog";

export default function ProjectPage() {
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Проекты</h1>
        <AddNewProjectDialog/>
      </div>
    </div>
  );
}
