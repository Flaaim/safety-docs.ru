"use client";

import React, {useEffect, useState} from "react";
import {CreateProductDTO} from "@/interfaces/product.interface";
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
import {ProductMultipleFormats} from "@/components/Admin/Dashboard/Docs/Format/product-formats";



export default function AddProductDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [selectedFormats, setSelectedFormats] = useState<string[]>([]);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if (!open) {
      setSelectedFormats([]);
    }
  }, [open]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);
    const formData = new FormData(e.currentTarget);

    const product: Partial<CreateProductDTO> = {
      name: formData.get('name') as string,
      cipher: formData.get('cipher') as string,
      amount: formData.get('amount') as string,
      filename: formData.get('filename') as string,
      updatedAt: formData.get('updatedAt') as string,
      slug: formData.get('slug') as string,
      file: formData.get('file') as File,
      totalDocuments: Number(formData.get('totalDocuments')),
      formatDocuments: selectedFormats
    };

    try {
      await addProduct(token, product);
      e.currentTarget.reset();
      toast.success('Продукт успешно добавлен.');

      setSelectedFormats([]);
      setOpen(false);
      router.refresh();

    } catch (error: any) {
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
            <Label htmlFor="filename">Имя файла</Label>
            <Input id="filename"  name="filename" placeholder="Например: med100.1.rar" required />
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
          <div className="grid gap-2">
            <Label htmlFor="totalDocuments">Количество док-тов в архиве</Label>
            <Input id="totalDocuments" type="number" name="totalDocuments" required></Input>
          </div>
          <div className="grid gap-2">
            <ProductMultipleFormats  formats={selectedFormats} onChange={(value) => setSelectedFormats(value)}/>
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
