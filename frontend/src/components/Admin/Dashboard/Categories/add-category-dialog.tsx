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
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { toast } from "sonner";
import Cookies from "js-cookie";
import { Category } from "@/interfaces/category.interface";
import { addCategory, getCategoriesByDirection } from "@api/category";
import MDEditor from "@uiw/react-md-editor";

interface AddCategoryDialogProps {
  directionId: string;
  defaultParentId?: string;
}

export default function AddCategoryDialog({
  directionId,
  defaultParentId,
}: AddCategoryDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const [categories, setCategories] = useState<Category[]>([]);

  const [selectedParentId, setSelectedParentId] = useState<string>("none");
  const [textValue, setTextValue] = useState<string>("");

  const router = useRouter();
  const token = Cookies.get("admin_token");

  useEffect(() => {
    if (open) {
      setLoading(true);
      const initData = async () => {
        try {
          setSelectedParentId(defaultParentId || "none");

          if (directionId) {
            const cats = await getCategoriesByDirection(directionId, token);
            setCategories(flattenCategories(cats));
          }
        } catch (error) {
          const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
          toast.error(err.message);
        } finally {
          setLoading(false);
        }
      };
      initData();
    } else {
      setCategories([]);
      setSelectedParentId("none");
      setTextValue("");
    }
  }, [open, token, directionId, defaultParentId]);

  useEffect(() => {
    let cancelled = false;

    if (open) {
      const loadParents = async () => {
        try {
          const cats = await getCategoriesByDirection(directionId, token);
          if (!cancelled) {
            setCategories(flattenCategories(cats));
          }
        } catch {
          if (!cancelled) setCategories([]);
        }
      };
      void loadParents();
    }

    return () => {
      cancelled = true;
    };
  }, [open, directionId, token]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    // Собираем данные: тексты берем из FormData, а ID берем из надежных React стейтов
    const category: Partial<Category> = {
      title: formData.get("title") as string,
      description: formData.get("description") as string,
      text: textValue,
      directionId: directionId, // Берем из стейта
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

  const filteredParents = categories.filter((cat) => cat.directionId === directionId);

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
              <Label htmlFor="parentId">Родительская категория</Label>
              <Select
                name="parentId"
                value={selectedParentId}
                onValueChange={setSelectedParentId}
                disabled={loading || Boolean(defaultParentId)}
              >
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem value="none" className="text-muted-foreground italic">
                      Без родителя (корневая)
                    </SelectItem>
                    {filteredParents.map((cat: Category) => (
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
            <Button type="submit" disabled={loading}>
              {loading ? "Сохранение..." : "Создать"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}

function flattenCategories(categories: Category[]): Category[] {
  const result: Category[] = [];
  const walk = (nodes: Category[]) => {
    for (const node of nodes) {
      result.push(node);
      if (node.children?.length) {
        walk(node.children);
      }
    }
  };
  walk(categories);
  return result;
}
