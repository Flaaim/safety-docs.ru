"use client"

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {Dialog, DialogContent, DialogDescription, DialogFooter,  DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Import, Loader2} from "lucide-react";
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
  const [isFetching, setIsFetching] = useState<boolean>(false);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false)

  const [projectsCollection, setProjectsCollection] = useState<ProjectsCollection | null>(null)

  const router = useRouter();
  const token = Cookies.get("admin_token");
  useEffect(() => {
    if(open){
      setIsFetching(true);
      const initProjects = async () => {
        try {
          const data = await getAllProjects(token);
          const hasProjects = data && data.projects && data.projects.length > 0;

          if(!hasProjects){
            toast.error('Проекты не найдены. Необходимо их сначала добавить.');
          }
          setProjectsCollection(data);
        } catch (error) {
          toast.error(error instanceof Error ? error.message : "Ошибка загрузки проектов");
        } finally {
          setIsFetching(false);
        }
      }
      initProjects();
    }else {
      setProjectsCollection(null)
    }
  }, [open, token]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    formData.append('fileId', fileId);

    if (!formData.get('projectId')) {
      toast.error("Пожалуйста, выберите проект.");
      return;
    }

    try{
      await importContacts(token, formData);

      toast.success("Контакты успешно импортированы!");
      setOpen(false);
      router.refresh();
    }catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка при при импорте контактов");
    } finally {
      setIsSubmitting(false);
    }
  }

  const hasProjects = projectsCollection && projectsCollection.projects && projectsCollection.projects.length > 0;

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
        {isFetching ? (<div className="flex justify-center py-8">
          <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
        </div>) : (
          <form onSubmit={onSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="project">Проект</Label>
              <Select name="projectId">
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Выберите проект" />
                </SelectTrigger>
                <SelectContent>
                  {hasProjects && projectsCollection?.projects ? (
                    projectsCollection.projects.map((project: ProjectsDTO) => (
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
              <Button type="submit" disabled={isSubmitting || !hasProjects}>
                {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Создать
              </Button>
            </DialogFooter>
          </form>
        )}

      </DialogContent>
    </Dialog>
  );
}
