"use client";

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {addDirection} from "@api/direction";
import {toast} from "sonner";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {Plus} from "lucide-react";
import {Button} from "@/components/ui/button";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {Textarea} from "@/components/ui/textarea";
import {DirectionDTO} from "@/interfaces/direction.interface";
import MDEditor from '@uiw/react-md-editor';

export default function AddDirectionDialog(){
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [textValue, setTextValue] = useState<string>('');

  const router = useRouter();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>){
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);

    const direction:Partial<DirectionDTO> = {
      title: formData.get('title') as string,
      description: formData.get('description') as string,
      text: textValue,
      slug: formData.get('slug') as string
    };

    const token = Cookies.get("admin_token");
    try{
      await addDirection(token, direction);

      toast.success("Направление добавлено");
      setOpen(false);
      router.refresh();
    }catch (error){
      toast.error(error instanceof Error ? error.message : "Не удалось добавить направление");
    }finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Plus className="mr-2 h-4 w-4" /> Добавить
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px]">
        <DialogHeader>
          <DialogTitle>Новое направление</DialogTitle>
          <DialogDescription>
            Добавление нового направления на сайт.
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
          <div className="grid gap-2">
            <Label htmlFor="title">Название</Label>
            <Input id="title" name="title" placeholder="Напр: Охрана труда" required />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="description">Описание</Label>
            <Textarea id="description" name="description" required></Textarea>
          </div>
          <div className="grid gap-2" data-color-mode="light">
            <MDEditor
              value={textValue}
              onChange={(val) => setTextValue(val || '')}
              height={300}
              textareaProps={{
                placeholder: 'Введите текст в формате Markdown...'
              }}
            />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="slug">Slug (URL)</Label>
            <Input id="slug" name="slug" placeholder="ohrana-truda" required />
          </div>
          <DialogFooter>
            <Button type="submit" disabled={loading}>
              {loading ? "Сохранение..." : "Создать"}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
