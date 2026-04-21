"use client"

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogDescription, DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger
} from "@/components/ui/dialog";
import Dropzone from "react-dropzone";
import {ImageCollection, ProductImages} from "@/interfaces/product.interface";
import {toast} from "sonner";
import {addImages} from "@api/product";


export interface AddImageDialogProps {
  productId: string
}

export default function AddImagesDialog({productId}: AddImageDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [files, setFiles] = useState<File[]>([]);
  const [previews, setPreviews] = useState<string[]>([]);


  const router = useRouter();

  const token = Cookies.get("admin_token");

  const handleDrop = (acceptedFiles: File[]) => {
    setFiles(acceptedFiles)

    const newPreviews = acceptedFiles.map(file => URL.createObjectURL(file))
    setPreviews(newPreviews);


  }

  useEffect(() => {
    if(!open){
      files.forEach(file => URL.revokeObjectURL(file));
      setFiles([]);
      setPreviews([]);
    }
  }, [open]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>){
    e.preventDefault();
    setLoading(true);

    try{
      const formData = new FormData();

      files.forEach((file) => {
        formData.append(`images`, file);
      })

      const uploadedImages:ProductImages = {
        productId: productId as string,
        images: files
      }

      await addImages(token, uploadedImages)

      toast.success('Изображения успешно загружены.')
      setFiles([])
      setOpen(false)

      router.refresh()

    }catch (error){
      toast.error(error instanceof Error ? error.message : "Ошибка при загрузке изображений");
    }finally {
      setLoading(false);
    }

  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Добавление галереи</DialogTitle>
          <DialogDescription>
            Добавление изображений продукта
          </DialogDescription>
        </DialogHeader>
        <form onSubmit={onSubmit} className="grid gap-4 py-4">
        <Dropzone
          onDrop={handleDrop}
          accept={{ 'image/*': ['.png', '.jpg', '.jpeg', '.webp'] }}
          maxFiles={10}
          maxSize={5242880}
        >
          {({ getRootProps, getInputProps, isDragAccept }) => (
            <div
              {...getRootProps()}
              className={`border-2 border-dashed rounded-lg p-8 text-center cursor-pointer
              ${isDragAccept ? 'border-primary bg-primary/10' : 'border-gray-300'}`}
            >
              <input {...getInputProps()} />
              {isDragAccept ? (
                <p>Отпустите файлы для загрузки...</p>
              ) : (
                <p>Перетащите изображения сюда или кликните для выбора</p>
              )}
            </div>
          )}
        </Dropzone>
        {previews.length > 0 && (
          <div className="grid grid-cols-3 gap-2 mt-4">
            {previews.map((preview, idx) => (
              <img key={idx} src={preview} className="w-full h-20 object-cover rounded"  alt='Изображение'/>
            ))}
          </div>
        )}
        <DialogFooter>
          <Button type="submit" disabled={loading}>
            {loading ? "Сохранение..." : "Загрузить"}
          </Button>
        </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
