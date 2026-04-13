"use client";

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {ProductDTO, UpdateProductDTO} from "@/interfaces/product.interface";
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
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {getProductById, updateProduct} from "@api/product";
import {toast} from "sonner";
import {ProductMultipleFormats} from "@/components/Admin/Dashboard/Docs/Format/product-formats";

export interface EditProductDialogProps {
  productId: string
}

export default function EditProductDialog({productId}: EditProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [productData, setProductData] = useState<ProductDTO | null>(null);
  const [selectedFormats, setSelectedFormats] = useState<string[]>([]);
  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open) {
      const initProduct = async () => {
        setLoading(true);
        try{
          const product = await getProductById(productId);
          setProductData(product);
        }catch (error){
          toast.error(error instanceof Error ? error.message : "Ошибка при загрузке продукта.");
        }finally {
          setLoading(false);
        }
      };
      initProduct();
    }else {
      setProductData(null);
    }
  }, [open, token, productId]);

  useEffect(() => {
    if (productData) {
      setSelectedFormats(productData.formatDocuments || []);
    }
  }, [productData, productId]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const product: Partial<UpdateProductDTO> = {
      id: productId,
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
      await updateProduct(token, product);

      toast.success("Продукт успешно обновлен.");
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка обновления продукта.");
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
      <DialogContent className="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Изменение продукта</DialogTitle>
          <DialogDescription>
            Изменение продукта на странице.
          </DialogDescription>
        </DialogHeader>
        {loading || !productData ? (<div>Загрузка...</div>) : (
          <form key={productData.id} onSubmit={onSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="name">Название</Label>
              <Input id="name" type='text' name="name" defaultValue={productData.name} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="cipher">Шифр</Label>
              <Input id="cipher" type='text' name="cipher"  defaultValue={productData.cipher} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="amount">Цена</Label>
              <Input id="amount" type='number' name="amount"  defaultValue={productData.formattedPrice ? parseFloat(productData.formattedPrice) : 0} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="updatedAt">Дата обновления</Label>
              <Input id="updatedAt" type='date' name="updatedAt"
                     defaultValue={productData.updatedAt ? new Date(productData.updatedAt).toISOString().split('T')[0] : ''} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="slug">Slug</Label>
              <Input id="slug" type='text' name="slug" defaultValue={productData.slug}  required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="slug">Имя файла</Label>
              <Input id="filename" type='text' name="filename" defaultValue={productData.filename}  required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="file">Файл</Label>
              <Input id="file" type="file" name="file" />
              <p className="text-xs text-muted-foreground">Оставьте пустым, чтобы не менять файл</p>
            </div>
            <div className="grid gap-2">
              <Label htmlFor="totalDocuments">Количество док-тов в архиве</Label>
              <Input id="totalDocuments" type="number" name="totalDocuments" defaultValue={productData.totalDocuments}></Input>
            </div>
            <div className="grid gap-2">
              <Label>Форматы документов</Label>
              <ProductMultipleFormats
                formats={productData.formatDocuments}
                onChange={setSelectedFormats}
              />
            </div>
            <DialogFooter>
              <Button type="submit" disabled={loading}>
                {loading ? "Сохранение..." : "Изменить"}
              </Button>
            </DialogFooter>
          </form>
        )}
      </DialogContent>
    </Dialog>
  );
}
