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
import {getAllCategories, getCategoryBySlug, updateCategory} from "@api/category";
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
  const [categories, setCategories] = useState<CategoryDTO[]>([]);

  const [selectedDirectionId, setSelectedDirectionId] = useState<string>("");
  const [selectedParentId, setSelectedParentId] = useState<string>("none");

  const [textValue, setTextValue] = useState<string>('');


  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open){
      setLoading(true);
      const initCategory = async () => {
        try{
          const [categoryDTO, catData, dirCollection] = await Promise.all([
            getCategoryBySlug(slug, directionId, token),
            getAllCategories(token),
            getAllDirections(token)
          ]);

          setCategories(catData.categories);
          setDirectionCollection(dirCollection);
          setCategoryData(categoryDTO);

          setTextValue(categoryDTO.text || '');

          setSelectedDirectionId(categoryDTO.directionId);
          setSelectedParentId(categoryDTO.parentId || "none");
        }catch (error){
          toast.error(error instanceof Error ? error.message : "Ошибка загрузки категорий");
        }finally {
          setLoading(false);
        }
      };
      const initDirections = async () => {
        try{
          const directionCollection = await getAllDirections(token);
          setDirectionCollection(directionCollection);
        }catch (error){
          toast.error(error instanceof Error ? error.message : "Ошибка при загрузке директорий");
        }
      };

      initCategory();
      initDirections();

    }else{
      setCategoryData(null);
      setCategories([]);
      setDirectionCollection({directions: [], total: 0});
      setSelectedDirectionId("");
      setSelectedParentId("none");
      setTextValue('');
    }
  }, [open, token, slug, directionId]);


  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const category: Partial<CategoryDTO> = {
      id: id,
      title: formData.get('title') as string,
      description: formData.get('description') as string,
      text: textValue,
      directionId: selectedDirectionId,
      parentId: selectedParentId === "none" ? null : selectedParentId,
    };

    try {
      await updateCategory(token, category);
      toast.success('Категория обновлена');
      setOpen(false);
      setTimeout(() => {
        router.refresh();
      }, 300);

    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Не удалось обновить категорию');
    } finally {
      setLoading(false);
    }
  }
  const filteredParents = categories.filter(
    cat => cat.directionId === selectedDirectionId && cat.id !== id
  );

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
              <Textarea id="description" name="description" defaultValue={categoryData.description} required></Textarea>
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
            {categoryData.children && categoryData.children.length > 0 ? (
              // 1. ЕСЛИ ЕСТЬ ДЕТИ: Показываем только Направление + предупреждение
              <div className="grid gap-2">
                <Label htmlFor="directionId">Направление (Категория имеет подкатегории)</Label>
                <Select
                  name="directionId"
                  value={selectedDirectionId}
                  onValueChange={(val) => {
                    setSelectedDirectionId(val);
                    setSelectedParentId("none"); // На всякий случай сбрасываем родителя
                  }}
                >
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
                <p className="text-xs text-muted-foreground mt-1">
                  Чтобы сделать эту категорию дочерней, сначала удалите или перенесите её подкатегории.
                </p>
              </div>
            ) : (
              // 2. ЕСЛИ ДЕТЕЙ НЕТ (Пустая корневая или дочерняя): Разрешаем менять всё
              <div className="grid grid-cols-2 gap-4">
                <div className="grid gap-2">
                  <Label htmlFor="directionId">Направление</Label>
                  <Select
                    name="directionId"
                    value={selectedDirectionId}
                    onValueChange={(val) => {
                      setSelectedDirectionId(val);
                      setSelectedParentId("none"); // При смене направления обязательно сбрасываем родителя!
                    }}
                  >
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

                <div className="grid gap-2">
                  <Label htmlFor="parentId">Родительская категория</Label>
                  <Select
                    name="parentId"
                    value={selectedParentId}
                    onValueChange={setSelectedParentId}
                  >
                    <SelectTrigger className="w-full">
                      <SelectValue placeholder="Без родителя" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem value="none" className="text-muted-foreground italic">
                          Без родителя (корневая)
                        </SelectItem>
                        {filteredParents.map((cat: CategoryDTO) => (
                          <SelectItem key={cat.id} value={cat.id}>
                            {cat.title}
                          </SelectItem>
                        ))}
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                </div>
              </div>
            )}
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
