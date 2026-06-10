"use client"

import React, {useState} from "react";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";
import {Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import {Button} from "@/components/ui/button";
import {Import} from "lucide-react";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";

export default function AddImportContactsDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const router = useRouter();

  const token = Cookies.get("admin_token");

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button>
          <Import className="mr-2 h-4 w-4"/> Импорт
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Импорт контактов</DialogTitle>
          <DialogDescription>Импортируйте контакты в проект</DialogDescription>
        </DialogHeader>

      </DialogContent>
    </Dialog>
  );
}
