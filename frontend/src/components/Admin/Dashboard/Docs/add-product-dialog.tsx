"use client";

import React, {useState} from "react";
import {ProductCollection, CreateProductDTO} from "@/interfaces/product.interface";
import {useRouter} from "next/navigation";
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
import {Plus} from "lucide-react";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {toast} from "sonner";
import {addProduct} from "@api/product";


export default function AddProductDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<Error | null>(null);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);


    const formData = new FormData(e.currentTarget);
    const name = formData.get('name');
    const cipher = formData.get('cipher');
    const amount = formData.get('amount');
    const path = formData.get('path');
    const updatedAt = formData.get('updatedAt');
    const slug = formData.get('slug');
    const fileData = formData.get('file');

    const file = fileData instanceof File ? fileData : undefined;

    const product: Partial<CreateProductDTO> = {
      name: typeof name === 'string' ? name : undefined,
      cipher: typeof cipher === 'string' ? cipher : undefined,
      amount: typeof amount === 'string' ? amount : undefined,
      path: typeof path === 'string' ? path : undefined,
      updatedAt: typeof updatedAt === 'string' ? updatedAt : undefined,
      slug: typeof slug === 'string' ? slug : undefined,
      file: file instanceof File ? file : undefined
    };

    try {
      await addProduct(token, product);
      toast.success('Продукт успешно добавлен.');
      setOpen(false);
      router.refresh();

    } catch (error: Error) {
      toast.error(error.message);
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
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Новый продукт</DialogTitle>
          <DialogDescription>
            Добавление новой подборки документов на сайт
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="name">Название</Label>
            <Input id="name" name="name" placeholder="Комплект документов" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="cipher">Шифр</Label>
            <Input id="cipher" name="cipher" placeholder="Шифр" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="amount">Цена</Label>
            <Input id="amount" type='number' name="amount"  required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="path">Путь к файлу</Label>
            <Input id="path"  name="path" placeholder="Например: safety/medical/med100.1.rar" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="updatedAt">Дата обновления</Label>
            <Input id="updatedAt" type='date' name="updatedAt"  required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="slug">Slug</Label>
            <Input id="slug" name="slug" placeholder='Например: medical'  required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="file">Приложить архив с файлами</Label>
            <Input id="file" type='file' name="file"  required />
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
