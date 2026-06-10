"use client"

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {Dialog, DialogContent, DialogDescription, DialogFooter,  DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Import} from "lucide-react";
import {getAllProjects, importContacts} from "@api/distribution";
import {toast} from "sonner";
import {ProjectsCollection, ProjectsDTO} from "@/interfaces/distribution.interface";
import {Label} from "@/components/ui/label";
import {Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";

export interface AddImportContactsDialogProps {
  fileId: string
}
export default function AddImportContactsDialog({fileId}: AddImportContactsDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [projectsCollection, setProjectsCollection] = useState<ProjectsCollection>([])
  const router = useRouter();

  const token = Cookies.get("admin_token");
  useEffect(() => {
    if(open){
      setLoading(true);
      const initProjects = async () => {
        try {
          const data = await getAllProjects(token);

          setProjectsCollection(data?.projects || []);
        } catch (error) {
          toast.error(error instanceof Error ? error.message : "Ошибка загрузки проектов");
        } finally {
          setLoading(false);
        }
      }
      initProjects()
    }
  }, [open, token]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const projectId = formData.get("projectId") as string

    try{
      await importContacts(token, projectId, fileId);

      toast.success("Контакты успешно импортированы!");
      setOpen(false);
    }catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка при при импорте контактов");
    } finally {
      setLoading(false);
    }
  }
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Import className="mr-2 h-4 w-4"/> Импорт
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Импорт контактов</DialogTitle>
          <DialogDescription>Импортируйте контакты в проект</DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="project">Проект</Label>
            <Select name="projectId">
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Выберите проект" />
              </SelectTrigger>
              <SelectContent>
                {projectsCollection.length > 0 ? (
                  projectsCollection.map((project: ProjectsDTO) => (
                    <SelectItem key={project.id} value={project.id}>
                      {project.name}
                    </SelectItem>
                  ))
                ) : (
                  <SelectItem value="none" disabled>
                    Нет доступных проектов. Создайте проект.
                  </SelectItem>
                )}
              </SelectContent>
            </Select>
          </div>
          <DialogFooter>
            <Button type="submit" disabled={loading}>
              {loading ? "Загрузка..." : "Импорт"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
