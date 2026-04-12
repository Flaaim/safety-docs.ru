"use client";

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {Button} from "@/components/ui/button";
import {FilePlus} from "lucide-react";
import {
  Dialog,
  DialogContent, DialogDescription, DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {ProductFree, ProductFreeCollection} from "@/interfaces/product.interface";
import {toast} from "sonner";
import {Label} from "@/components/ui/label";
import {Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import {getFreeProducts} from "@api/product";
import {assignProduct} from "@api/category";
import {AssignCategory} from "@/interfaces/category.interface";


export interface AssignProductDialogProps {
  categoryId: string
}

export default function AssignProductDialog({categoryId}:AssignProductDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [productFreeCollection, setProductFreeCollection] = useState<ProductFreeCollection>([]);
  const router = useRouter();

  const token = Cookies.get("admin_token");
  useEffect(() => {
    if(open){
      setLoading(true);
      const initProducts = async () => {
        try{
          const productFreeCollection = await getFreeProducts(token);
          setProductFreeCollection(productFreeCollection);
        }catch (error){
          toast.error(error instanceof Error ? error.message : "Ошибка загрузки продуктов");
        }finally {
          setLoading(false);
        }
      };
      initProducts();
    }else {
      setLoading(false);
      setProductFreeCollection([]);
    }
  }, [open, token]);
  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const productId = formData.get('productId');

    const data: AssignCategory = {
      productId: typeof productId === 'string' ? productId : '',
      categoryId: categoryId
    };

    try {
      await assignProduct(token, data);

      toast.success('Продукт успешно привязан');
      setOpen(false);

      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка при при привязке продукта");
    } finally {
      setLoading(false);
    }

  }
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
      <Button>
        <FilePlus className="h-4 w-4"/>
      </Button>
    </DialogTrigger>
      <DialogContent className="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Привязать продукт к категории</DialogTitle>
          <DialogDescription>
            Выберите продукт, который хотите привязать к категории.
          </DialogDescription>
        </DialogHeader>

        {loading ? (<div>Загрузка...</div>) : (
          <form key={categoryId} onSubmit={onSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="product">Продукт</Label>
              <Select name='productId'>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="Выберите продукт" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    {productFreeCollection.map((prod: ProductFree) => (
                      <SelectItem key={prod.id} value={prod.id}>{prod.name}</SelectItem>
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
