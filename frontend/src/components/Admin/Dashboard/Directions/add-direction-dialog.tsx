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


export default function AddDirectionDialog(){
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const router = useRouter();

  async function onSubmit(e: React.FormEvent<HTMLFormElement>){
    e.preventDefault();
    setLoading(true);

    const formData = new FormData(e.currentTarget);
    const title = formData.get('title');
    const description = formData.get('description');
    const text = formData.get('text')
    const slug = formData.get('slug');


    const direction:Partial<DirectionDTO> = {
      title: typeof title === 'string' ? title : undefined,
      description: typeof description === 'string' ? description : undefined,
      text: typeof text === 'string' ? text : undefined,
      slug: typeof slug === 'string' ? slug : undefined
    }

    const token = Cookies.get("admin_token");
    try{
      await addDirection(token, direction)

      toast.success("Направление добавлено");
      setOpen(false);
      router.refresh();
    }catch (error){
      toast.error("Не удалось добавить направление");
    }finally {
      setLoading(false)
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
            <Textarea id="description" name="description" rows='5' required></Textarea>
          </div>
          <div className="grid gap-2">
            <Label htmlFor="text">Текст на странице</Label>
            <Textarea id="text" name="text" rows='20' required></Textarea>
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
  )
}
