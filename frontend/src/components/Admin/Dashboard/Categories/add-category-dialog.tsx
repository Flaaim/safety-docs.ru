"use client";

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import {
  Dialog,
  DialogContent,
  DialogDescription, DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {Textarea} from "@/components/ui/textarea";
import {DirectionCollection, DirectionDTO} from "@/interfaces/direction.interface";
import {Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import {getAllDirections} from "@api/direction";
import {toast} from "sonner";
import Cookies from "js-cookie";
import {CategoryDTO} from "@/interfaces/category.interface";
import {addCategory} from "@api/category";
import MDEditor from '@uiw/react-md-editor';

export default function AddCategoryDialog(){
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<Error | null>(null);
  const [directionCollection, setDirectionCollection] = useState<DirectionCollection>({directions: [], total: 0});
  const [textValue, setTextValue] = useState<string>('');
  const router = useRouter();


  const token = Cookies.get("admin_token");


  useEffect(() => {
    if(open){
      setLoading(true);
      const initDirectionCollection = async () => {
        try{
          const data = await getAllDirections(token);

          setDirectionCollection(data);

        }catch (error){
          toast.error(error instanceof Error ? error.message : "Ошибка при получении данных");
          setError(error);
        }finally {
          setLoading(false);
        }
      };
      initDirectionCollection();
    }else {
      setDirectionCollection({directions: [], total: 0});
    }


  }, [open, token]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const category: Partial<CategoryDTO> = {
      title: formData.get('title') as string,
      description: formData.get('description') as string,
      text: textValue,
      slug: formData.get('slug') as string,
      directionId: formData.get('directionId') as string
    };
    try {
      await addCategory(token, category);

      toast.success("Направление добавлено");
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка при сохранении");
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Plus className="mr-2 h-4 w-4" /> Добавить
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px]">
        <DialogHeader>
          <DialogTitle>Новая категория</DialogTitle>
          <DialogDescription>
            Добавление новой категории на сайт.
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="title">Название</Label>
            <Input id="title" name="title" placeholder="Напр: Служба охраны труда" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="description">Описание</Label>
            <Textarea id="description" name="description" required></Textarea>
          </div>
          <div className="grid gap-2" data-color-mode="light">
            <MDEditor
              value={textValue}
              onChange={(val) => setTextValue(val || '')}
              height={300}
              textareaProps={{
                placeholder: 'Введите текст в формате Markdown...'
              }}
            />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="slug">Slug (URL)</Label>
            <Input id="slug" name="slug" placeholder="education" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="directionId">Направление</Label>
            {error  ? (<div className="text-destructive text-sm">Ошибка загрузки: {error.message}</div>) : (
              <Select name='directionId'>
              <SelectTrigger className="w-full" disabled={loading || !directionCollection.directions.length}>
                <SelectValue placeholder={loading ? "Загрузка..." : "Выберите направление"} />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  {directionCollection.directions.map((dir: DirectionDTO) => (
                    <SelectItem key={dir.slug} value={dir.id}>{dir.title}</SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>
            )}


          </div>
          <DialogFooter>
            <Button type="submit" disabled={loading}>
              {loading ? "Сохранение..." : "Создать"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
