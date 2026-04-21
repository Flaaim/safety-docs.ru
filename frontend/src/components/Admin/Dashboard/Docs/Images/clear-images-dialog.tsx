"use client"

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {
  AlertDialog, AlertDialogAction, AlertDialogCancel,
  AlertDialogContent, AlertDialogDescription, AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger
} from "@/components/ui/alert-dialog";
import {Button} from "@/components/ui/button";
import {X} from "lucide-react";
import {toast} from "sonner";
import {clearImages} from "@api/product";

export interface ClearImagesDialogProps {
  productId: string
}


export default function ClearImagesDialog({productId}:ClearImagesDialogProps) {
  const [loading, setLoading] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  async function onSubmit(e: React.MouseEvent) {
    e.preventDefault();
    try {
      setLoading(true);
      await clearImages(token, productId);

      toast.success('Изображения успешно удалены.');
      setLoading(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка. Изображения не удалены.");
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
          <AlertDialogTitle>Очистить галерею?</AlertDialogTitle>
          <AlertDialogDescription>
            Данное действие полностью очистит галерею.
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
