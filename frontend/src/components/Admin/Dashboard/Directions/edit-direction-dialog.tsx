"use client";

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import {
  Dialog,
  DialogContent,
  DialogDescription, DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Edit} from "lucide-react";
import Cookies from "js-cookie";
import {getDirectionBySlug, updateDirection} from "@api/direction";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {Textarea} from "@/components/ui/textarea";
import {DirectionDTO} from "@/interfaces/direction.interface";
import {toast} from "sonner";
import MDEditor from '@uiw/react-md-editor';

export interface EditDirectionDialogProps {
  slug: string
  id: string
}

export default function EditDirectionDialog({slug, id}: EditDirectionDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [directionData, setDirectionData] = useState<DirectionDTO | null>(null);
  const [textValue, setTextValue] = useState<string>('');

  const router = useRouter();

  const token = Cookies.get("admin_token");

  useEffect(() => {
    if(open){
      setLoading(true);
      const initDirection = async () => {
        try{
          const directionDTO = await getDirectionBySlug(slug, token);
          setDirectionData(directionDTO);
          setTextValue(directionDTO.text || '');
        }catch (error){
          toast.error('Не удалось загрузить направление');
        }finally {
          setLoading(false);
        }
      };
      initDirection();
    }else {
      setDirectionData(null);
      setTextValue('');
    }

  }, [open]);

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

    try{
      await updateDirection(token, id, direction);
      toast.success('Направление обновлено');
      setOpen(false);

      setTimeout(() => {
        router.refresh();
      }, 100);

    }catch (error){
      toast.error('Не удалось обновить направление');
    }finally {
      setLoading(false);
    }
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Edit className="h-4 w-4"/>
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px]">
        <DialogHeader>
          <DialogTitle>Изменение направления</DialogTitle>
          <DialogDescription>
            Изменение направления на сайте.
          </DialogDescription>
        </DialogHeader>
        {loading || !directionData ? (<div>Загрузка....</div>) : (
          <form key={directionData.id} onSubmit={onSubmit} className="grid gap-4 py-4">
            <div className="grid gap-2">
              <Label htmlFor="title">Название</Label>
              <Input id="title" name="title" placeholder="Напр: Охрана труда" defaultValue={directionData.title} required />
            </div>
            <div className="grid gap-2">
              <Label htmlFor="description">Описание</Label>
              <Textarea id="description" name="description" defaultValue={directionData.description} required></Textarea>
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
              <Input id="slug" name="slug" placeholder="ohrana-truda" defaultValue={directionData.slug} required/>
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
