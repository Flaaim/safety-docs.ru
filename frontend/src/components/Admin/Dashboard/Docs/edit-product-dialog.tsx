"use client"

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {ProductDTO} from "@/interfaces/product.interface";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Edit} from "lucide-react";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {getProductById} from "@api/product";
import {toast} from "sonner";

export interface EditProductDialogProps {
  productId: string
}

export default function EditProductDialog({productId}: EditProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false)
  const [loading, setLoading] = useState<boolean>(false)
  const [productData, setProductData] = useState<ProductDTO | null>(null)
  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open) {
      const initProduct = async () => {
        setLoading(true)
        try{
          const product = await getProductById(productId);
          setProductData(product)
        }catch (error: any){
          toast.error(error.message)
        }finally {
          setLoading(false)
        }
      }
      initProduct()
    }else {
      setProductData(null)
    }
  }, [open]);

  function onSubmit(e: React.FormEvent<HTMLFormElement>) {

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
              {productData.file && (<div className="flex items-center gap-2 p-2 border rounded-md bg-muted/50">
                <span className="text-sm truncate">
                    Текущий: <strong>{productData.file}</strong>
                </span>
              </div>)}
              <Input id="file" type="file" name="file" />
              <p className="text-xs text-muted-foreground">Оставьте пустым, чтобы не менять файл</p>
            </div>
          </form>
        )}
      </DialogContent>
    </Dialog>
  )
}
