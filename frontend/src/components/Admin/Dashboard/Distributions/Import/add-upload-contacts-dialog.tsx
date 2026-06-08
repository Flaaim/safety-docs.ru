"use client"

import React, {useState} from "react";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";
import {Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {UploadContactsDTO} from "@/interfaces/distribution.interface";

export default function AddUploadContactsDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);
    const formData = new FormData(e.currentTarget);

    const file: UploadContactsDTO = {
      file: formData.get("file") as File,
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
          <DialogTitle>Загрузка файла</DialogTitle>
          <DialogDescription>Загрузка файла с контактами на сайт</DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="file">Файл</Label>
            <Input id="file" type="file" name="file" required />
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
