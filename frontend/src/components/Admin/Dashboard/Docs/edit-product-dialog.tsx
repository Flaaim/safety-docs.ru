"use client";

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {CreateProductDTO, ProductDTO, UpdateProductDTO} from "@/interfaces/product.interface";
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

export interface EditProductDialogProps {
  productId: string
}

export default function EditProductDialog({productId}: EditProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [productData, setProductData] = useState<ProductDTO | null>(null);
  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open) {
      const initProduct = async () => {
        setLoading(true);
        try{
          const product = await getProductById(productId);
          setProductData(product);
        }catch (error: any){
          toast.error(error.message);
        }finally {
          setLoading(false);
        }
      };
      initProduct();
    }else {
      setProductData(null);
    }
  }, [open]);

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

    const product: Partial<UpdateProductDTO> = {
      id: productId,
      name: typeof name === 'string' ? name : undefined,
      cipher: typeof cipher === 'string' ? cipher : undefined,
      amount: typeof amount === 'string' ? amount : undefined,
      path: typeof path === 'string' ? path : undefined,
      updatedAt: typeof updatedAt === 'string' ? updatedAt : undefined,
      slug: typeof slug === 'string' ? slug : undefined,
      file: file instanceof File ? file : undefined
    };

    try {
      await updateProduct(token, product);

      toast.success("Продукт успешно обновлен.");
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
              <Label htmlFor="file">Файл</Label>
              {productData.file && (<Input id='path' name="path" defaultValue={productData.file} readOnly={true}/>)}
              <Input id="file" type="file" name="file" />
              <p className="text-xs text-muted-foreground">Оставьте пустым, чтобы не менять файл</p>
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
