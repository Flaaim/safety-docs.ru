"use client";

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {X} from "lucide-react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger
} from "@/components/ui/alert-dialog";
import {Button} from "@/components/ui/button";
import {refuseProduct} from "@api/category";
import {toast} from "sonner";
export interface RefuseProductDialogProps {
  categoryId: string
}
export default function RefuseProductDialog({categoryId}:RefuseProductDialogProps) {
  const [loading, setLoading] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);


  const router = useRouter();

  const token = Cookies.get("admin_token");
  async function onSubmit(e: React.MouseEvent) {
    e.preventDefault();
    try {
      setLoading(true);
      await refuseProduct(token, categoryId);

      toast.success('Продукт успешно отвязан');
      setLoading(false);
      router.refresh();
    } catch (error: any) {
        toast.error(error.message);
    }finally {
      setLoading(false);
    }
  }

  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>
        <Button>
          <X className="h-4 w-4"/>
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Отвязать продукт?</AlertDialogTitle>
          <AlertDialogDescription>
            Данное действие отвяжет продукт от категории.
          </AlertDialogDescription>
        </AlertDialogHeader>
        {loading ? <div>Загрузка...</div> : (<AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction onClick={onSubmit}>Continue</AlertDialogAction>
        </AlertDialogFooter>)}
      </AlertDialogContent>
    </AlertDialog>
  );
}
