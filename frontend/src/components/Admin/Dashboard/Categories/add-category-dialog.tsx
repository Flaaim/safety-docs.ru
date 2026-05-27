"use client";

import React, { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { DirectionCollection, DirectionDTO } from "@/interfaces/direction.interface";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { getAllDirections } from "@api/direction";
import { toast } from "sonner";
import Cookies from "js-cookie";
import { CategoryDTO } from "@/interfaces/category.interface";
import { addCategory, getAllCategories } from "@api/category";
import MDEditor from "@uiw/react-md-editor";

export default function AddCategoryDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<Error | null>(null);

  const [directionCollection, setDirectionCollection] = useState<DirectionCollection>({
    directions: [],
    total: 0,
  });
  const [categories, setCategories] = useState<CategoryDTO[]>([]);

  // === КОНТРОЛИРУЕМЫЕ СТЕЙТЫ ДЛЯ СЕЛЕКТОВ ===
  const [selectedDirectionId, setSelectedDirectionId] = useState<string>("");
  const [selectedParentId, setSelectedParentId] = useState<string>("none"); // По умолчанию "none"
  const [textValue, setTextValue] = useState<string>("");

  const router = useRouter();
  const token = Cookies.get("admin_token");

  useEffect(() => {
    if (open) {
      setLoading(true);
      const initData = async () => {
        try {
          const [dirData, catData] = await Promise.all([
            getAllDirections(token),
            getAllCategories(token),
          ]);

          setDirectionCollection(dirData);
          setCategories(catData.categories);
        } catch (error) {
          const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
          toast.error(err.message);
          setError(err);
        } finally {
          setLoading(false);
        }
      };
      initData();
    } else {
      setDirectionCollection({ directions: [], total: 0 });
      setCategories([]);
      setSelectedDirectionId("");
      setSelectedParentId("none"); // Обязательно сбрасываем при закрытии
      setTextValue("");
    }
  }, [open, token]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    // Собираем данные: тексты берем из FormData, а ID берем из надежных React стейтов
    const category: Partial<CategoryDTO> = {
      title: formData.get("title") as string,
      description: formData.get("description") as string,
      text: textValue,
      directionId: selectedDirectionId, // Берем из стейта
      parentId: selectedParentId === "none" ? undefined : selectedParentId, // Конвертируем "none" в undefined
    };

    try {
      await addCategory(token, category);
      toast.success("Категория добавлена");
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка при сохранении");
    } finally {
      setLoading(false);
    }
  }

  const filteredParents = categories.filter((cat) => cat.directionId === selectedDirectionId);

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Plus className="mr-2 h-4 w-4" /> Добавить
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px] max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Новая категория</DialogTitle>
          <DialogDescription>Добавление новой категории на сайт.</DialogDescription>
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
              onChange={(val) => setTextValue(val || "")}
              height={250}
              textareaProps={{
                placeholder: "Введите текст в формате Markdown...",
              }}
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="grid gap-2">
              <Label htmlFor="directionId">Направление</Label>
              {error ? (
                <div className="text-destructive text-sm">Ошибка загрузки</div>
              ) : (
                <Select
                  name="directionId"
                  value={selectedDirectionId} // Привязка стейта
                  onValueChange={(val) => {
                    setSelectedDirectionId(val);
                    setSelectedParentId("none"); // СБРОС родителя при смене направления!
                  }}
                >
                  <SelectTrigger
                    className="w-full"
                    disabled={loading || !directionCollection.directions.length}
                  >
                    <SelectValue placeholder={loading ? "Загрузка..." : "Выберите направление"} />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      {directionCollection.directions.map((dir: DirectionDTO) => (
                        <SelectItem key={dir.slug} value={dir.id}>
                          {dir.title}
                        </SelectItem>
                      ))}
                    </SelectGroup>
                  </SelectContent>
                </Select>
              )}
            </div>

            <div className="grid gap-2">
              <Label htmlFor="parentId">Родительская категория</Label>
              <Select
                name="parentId"
                value={selectedParentId} // Привязка стейта
                onValueChange={setSelectedParentId} // Обновление стейта
                disabled={!selectedDirectionId || loading}
              >
                <SelectTrigger className="w-full">
                  <SelectValue
                    placeholder={
                      !selectedDirectionId ? "Сначала выберите направление" : "Без родителя"
                    }
                  />
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

          <DialogFooter>
            <Button type="submit" disabled={loading || !selectedDirectionId}>
              {loading ? "Сохранение..." : "Создать"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
