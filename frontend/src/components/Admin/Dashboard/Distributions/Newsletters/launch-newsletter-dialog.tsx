"use client";


import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription,
  AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger} from "@/components/ui/alert-dialog";
import {Button} from "@/components/ui/button";
import {Loader2, SendHorizontal, Trash} from "lucide-react";
import {removeContactsFile} from "@api/distribution";
import {toast} from "sonner";

export interface LaunchNewsletterDialogProps {
  newsletterId: string
}

export default function LaunchNewsletterDialog({newsletterId} : LaunchNewsletterDialogProps) {
  const [loading, setLoading] = useState<boolean>(false);
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  async function onSubmit(e: React.MouseEvent) {
    e.preventDefault();

    try {
      setLoading(true);
      await launchNewsletter(token, newsletterId);

      toast.success("Рассылка успешно отправлена");
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка. Рассылка не отправлена");
    } finally {
      setLoading(false);
    }
  }
  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>
        <Button>
          <SendHorizontal  className="h-4 w-4" />
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Запустить рассылку?</AlertDialogTitle>
          <AlertDialogDescription>
            Запустить рассылку по контактам
          </AlertDialogDescription>
        </AlertDialogHeader>
        {loading ? (
          <div>Загрузка...</div>
        ) : (
          <AlertDialogFooter>
            <AlertDialogCancel>Отмена</AlertDialogCancel>
            <AlertDialogAction
              onClick={onSubmit}
              disabled={loading}
              className="bg-red-600 hover:bg-red-700 text-white"
            >
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Запустить
            </AlertDialogAction>
          </AlertDialogFooter>
        )}
      </AlertDialogContent>
    </AlertDialog>
  );
}
