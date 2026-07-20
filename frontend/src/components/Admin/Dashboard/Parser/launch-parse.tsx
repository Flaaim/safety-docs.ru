"use client";

import { CategoryChildren } from "@/types/category";
import { useRouter } from "next/navigation";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import React, { useState } from "react";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { launchParser } from "@api/parser";
import { ParserDataInterface } from "@/interfaces/parser.interface";

interface LaunchParseProps {
  categories: CategoryChildren[];
  token?: string;
}

export default function LaunchParse({ categories, token }: LaunchParseProps) {
  const [loading, setLoading] = useState(false);
  const [categoryValue, setCategoryValue] = useState<string>("");
  const router = useRouter();

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setLoading(true);

    const form = e.currentTarget;
    const formData = new FormData(e.currentTarget);
    const amountValue = formData.get("amount");
    const parserData: ParserDataInterface = {
      categoryId: formData.get("categoryId") as string,
      url: formData.get("url") as string,
      amount: amountValue ? Number(amountValue) : 0,
      cookie: formData.get("cookie") as string,
    };

    try {
      await launchParser(token, parserData);
      toast.success("Парсер запущен");

      form.reset();
      setCategoryValue("");
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Ошибка запуска парсинга");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">Парсер документов</CardTitle>
        <CardDescription>Укажите данные для парсинга</CardDescription>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid gap-2">
            <Label htmlFor="category">Категория</Label>
            <Select
              disabled={loading || categories.length === 0}
              name="categoryId"
              value={categoryValue}
              onValueChange={setCategoryValue}
              required
            >
              <SelectTrigger id="category" className="w-full">
                <SelectValue placeholder="Выберите категорию" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  {categories.map((category) => (
                    <SelectItem
                      key={category.id}
                      value={category.id}
                      className="text-muted-foreground"
                    >
                      <span style={{ paddingLeft: `12px` }}>{category.title}</span>
                    </SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
          <div className="grid gap-2">
            <Label htmlFor="amount">Цена, ₽</Label>
            <Input
              id="amount"
              type="number"
              step="25"
              min="0"
              name="amount"
              disabled={loading}
              required
            />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="url">Ссылка</Label>
            <Input
              id="url"
              name="url"
              type="text"
              disabled={loading}
              placeholder="Формат: /system/content/subrubricator/7/101/17/"
              required
            />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="cookie">Cookie</Label>
            <Textarea id="cookie" name="cookie" disabled={loading} required></Textarea>
          </div>
          <CardFooter className="px-0 pb-0 pt-2">
            <Button type="submit" disabled={loading}>
              {loading ? "Сохранение..." : "Создать"}
            </Button>
          </CardFooter>
        </form>
      </CardContent>
    </Card>
  );
}
