"use client";

import React, {useEffect, useState} from "react";
import {CategoryDTO} from "@/interfaces/category.interface";
import Cookies from "js-cookie";
import {
  Dialog,
  DialogContent,
  DialogDescription, DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Edit} from "lucide-react";
import {getCategoryBySlug, updateCategory} from "@api/category";
import {toast} from "sonner";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {Textarea} from "@/components/ui/textarea";
import {Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import {getAllDirections} from "@api/direction";
import {DirectionCollection, DirectionDTO} from "@/interfaces/direction.interface";
import {useRouter} from "next/navigation";
import MDEditor from '@uiw/react-md-editor';


export interface EditCategoryDialogProps  {
  slug: string,
  id: string,
  directionId: string
}

export default function EditCategoryDialog({slug, id, directionId}: EditCategoryDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [categoryData, setCategoryData] = useState<CategoryDTO | null>(null);
  const [directionCollection, setDirectionCollection] = useState<DirectionCollection>({directions: [], total: 0});
  const [textValue, setTextValue] = useState<string>('');
  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open){
      setLoading(true);
      const initCategory = async () => {
        try{
          const categoryDTO = await getCategoryBySlug(slug, directionId, token);
          setCategoryData(categoryDTO);
          setTextValue(categoryDTO.text || '');
        }catch (error){
          toast.error('Не удалось загрузить категорию');
        }finally {
          setLoading(false);
        }
      };
      const initDirections = async () => {
        try{
          const directionCollection = await getAllDirections(token);
          setDirectionCollection(directionCollection);
        }catch (error){
          toast.error('Не удалось загрузить директории');
        }
      };

      initCategory();
      initDirections();

    }else{
      setCategoryData(null);
      setDirectionCollection({directions: [], total: 0});
      setTextValue('');
    }
  }, [open]);


  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const category: Partial<CategoryDTO> = {
      id: id,
      title: formData.get('title') as string,
      description: formData.get('description') as string,
      text: textValue,
      slug: formData.get('slug') as string,
      directionId: formData.get('directionId') as string
    };

    try {
      await updateCategory(token, category);
      toast.success('Категория обновлена');
      setOpen(false);

      router.refresh();
    } catch (error) {
      toast.error('Не удалось обновить категорию');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Edit className="h-4 w-4"/>
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px]">
        <DialogHeader>
          <DialogTitle>Изменение категории</DialogTitle>
          <DialogDescription>
            Изменение страницы категории на сайте.
          </DialogDescription>
        </DialogHeader>

        {loading || !categoryData ? (<div>Загрузка...</div>) : (
          <form key={categoryData.id} onSubmit={onSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="title">Название</Label>
              <Input id="title" name="title" placeholder="Название..." defaultValue={categoryData.title} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="description">Описание</Label>
              <Textarea id="description" name="description" rows='5' defaultValue={categoryData.description} required></Textarea>
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
              <Input id="slug" name="slug" placeholder="ohrana-truda" defaultValue={categoryData.slug} required/>
            </div>
            <div className="grid gap-2">
              <Label htmlFor="direction">Направление</Label>
              <Select name='directionId' defaultValue={categoryData.directionId}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Выберите направление" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    {directionCollection.directions.map((dir: DirectionDTO) => (
                      <SelectItem key={dir.id} value={dir.id}>{dir.title}</SelectItem>
                    ))}
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
            <DialogFooter>
              <Button type="submit" disabled={loading}>
                {loading ? "Сохранение..." : "Сохранить"}
              </Button>
            </DialogFooter>
          </form>
        )}
      </DialogContent>
    </Dialog>
  );
}
