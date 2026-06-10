"use client"

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription,
  AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger} from "@/components/ui/alert-dialog";
import {Button} from "@/components/ui/button";
import {Loader2, Trash} from "lucide-react";
import {toast} from "sonner";
import {removeContactsFile} from "@api/distribution";

export interface DeleteContactsFileDialogProps {
  fileId: string
}

export default function DeleteContactsFileDialog({fileId}: DeleteContactsFileDialogProps){
  const [loading, setLoading] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  async function onSubmit(e: React.MouseEvent) {
    e.preventDefault();
    try {
      setLoading(true);
      await removeContactsFile(token, fileId);

      toast.success("Файл успешно удален");
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка. Файл не удален.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>
        <Button>
          <Trash className="h-4 w-4" />
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Удалить файл?</AlertDialogTitle>
          <AlertDialogDescription>
            Данное действие полностью удалит файл с контактами.
          </AlertDialogDescription>
        </AlertDialogHeader>
        {loading ? (
          <div>Загрузка...</div>
        ) : (
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={onSubmit}
              disabled={loading}
              className="bg-red-600 hover:bg-red-700 text-white"
            >
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Удалить
            </AlertDialogAction>
          </AlertDialogFooter>
        )}
      </AlertDialogContent>
    </AlertDialog>
  );
}
