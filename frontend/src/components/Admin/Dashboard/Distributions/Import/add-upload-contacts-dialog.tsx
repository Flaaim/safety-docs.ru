"use client"

import React, {useEffect, useState} from "react";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";
import {Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {UploadContactsDTO} from "@/interfaces/distribution.interface";
import {toast} from "sonner";
import {uploadContacts} from "@api/distribution";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";

export default function AddUploadContactsDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);
    const formData = new FormData(e.currentTarget);

    try {
      await uploadContacts(token, formData);
      setOpen(false);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка загрузке файла.");
    } finally {
      setLoading(false);
    }

  }



  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Plus className="mr-2 h-4 w-4"/> Добавить
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Загрузка файла</DialogTitle>
          <DialogDescription>Загрузка файла с контактами на сайт</DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="file">Файл</Label>
            <Input id="file" type="file" name="file" required/>
          </div>
          <DialogFooter>
            <Button type="submit" disabled={loading}>
              {loading ? "Загрузка..." : "Загрузить"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
