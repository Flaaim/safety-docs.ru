"use client";

import React, { useCallback, useMemo, useRef, useState } from "react";
import { useRouter } from "next/navigation";
import Cookies from "js-cookie";
import { toast } from "sonner";
import { FileUp, Trash2, Upload } from "lucide-react";
import { Category } from "@/types/category";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { isAcceptedTemplateFile, uploadTemplatesBulk } from "@api/document";

interface BulkUploadDocumentsProps {
  directionId: string;
  categories: Category[];
  defaultCategoryId: string;
}

interface CategoryOption {
  id: string;
  title: string;
  depth: number;
  isLeaf: boolean;
}

function buildCategoryOptions(categories: Category[]): CategoryOption[] {
  const options: CategoryOption[] = [];

  const walk = (nodes: Category[], depth: number) => {
    for (const node of nodes) {
      const hasChildren = (node.children?.length ?? 0) > 0;
      options.push({
        id: node.id,
        title: node.title,
        depth,
        isLeaf: !hasChildren,
      });

      if (hasChildren) {
        walk(node.children, depth + 1);
      }
    }
  };

  walk(categories, 0);
  return options;
}

function formatFileSize(bytes: number): string {
  if (bytes < 1024) {
    return `${bytes} Б`;
  }

  if (bytes < 1024 * 1024) {
    return `${(bytes / 1024).toFixed(1)} КБ`;
  }

  return `${(bytes / (1024 * 1024)).toFixed(1)} МБ`;
}

export default function BulkUploadDocuments({
  directionId,
  categories,
  defaultCategoryId,
}: BulkUploadDocumentsProps) {
  const router = useRouter();
  const inputRef = useRef<HTMLInputElement>(null);

  const categoryOptions = useMemo(() => buildCategoryOptions(categories), [categories]);
  const leafCategories = useMemo(
    () => categoryOptions.filter((option) => option.isLeaf),
    [categoryOptions]
  );

  const initialCategoryId = useMemo(() => {
    const defaultOption = categoryOptions.find((option) => option.id === defaultCategoryId);
    if (defaultOption?.isLeaf) {
      return defaultCategoryId;
    }

    return leafCategories[0]?.id ?? "";
  }, [categoryOptions, defaultCategoryId, leafCategories]);

  const [selectedCategoryId, setSelectedCategoryId] = useState(initialCategoryId);
  const [amount, setAmount] = useState("150");
  const [files, setFiles] = useState<File[]>([]);
  const [isDragging, setIsDragging] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);

  const token = Cookies.get("admin_token");

  const addFiles = useCallback((incoming: FileList | File[]) => {
    const next = Array.from(incoming);
    const valid: File[] = [];
    const rejected: string[] = [];

    for (const file of next) {
      if (isAcceptedTemplateFile(file)) {
        valid.push(file);
      } else {
        rejected.push(file.name);
      }
    }

    if (rejected.length > 0) {
      toast.error(`Неподдерживаемый формат: ${rejected.join(", ")}. Допустимы только .doc и .docx`);
    }

    if (valid.length === 0) {
      return;
    }

    setFiles((current) => {
      const names = new Set(current.map((file) => file.name));
      const merged = [...current];

      for (const file of valid) {
        if (!names.has(file.name)) {
          merged.push(file);
          names.add(file.name);
        }
      }

      return merged;
    });
  }, []);

  const removeFile = (name: string) => {
    setFiles((current) => current.filter((file) => file.name !== name));
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();

    if (!selectedCategoryId) {
      toast.error("Выберите конечную категорию для загрузки");
      return;
    }

    const selected = categoryOptions.find((option) => option.id === selectedCategoryId);
    if (!selected?.isLeaf) {
      toast.error("Загрузка разрешена только в конечные категории без подкатегорий");
      return;
    }

    const parsedAmount = Number(amount);
    if (!Number.isFinite(parsedAmount) || parsedAmount <= 0) {
      toast.error("Укажите корректную цену больше 0");
      return;
    }

    if (files.length === 0) {
      toast.error("Добавьте хотя бы один файл");
      return;
    }

    setUploading(true);
    setProgress(0);

    try {
      await uploadTemplatesBulk(
        token,
        directionId,
        selectedCategoryId,
        parsedAmount,
        files,
        setProgress
      );

      toast.success(`Загружено файлов: ${files.length}`);
      setFiles([]);
      setProgress(0);
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Не удалось загрузить документы");
    } finally {
      setUploading(false);
    }
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Upload className="h-5 w-5" />
          Массовая загрузка документов
        </CardTitle>
        <CardDescription>
          Загрузите один или несколько файлов Word (.doc, .docx) в конечную категорию. Файлы с
          совпадающим именем будут перезаписаны.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="grid gap-2">
              <Label htmlFor="bulk-category">Категория</Label>
              <Select
                value={selectedCategoryId}
                onValueChange={setSelectedCategoryId}
                disabled={uploading || leafCategories.length === 0}
              >
                <SelectTrigger id="bulk-category" className="w-full">
                  <SelectValue placeholder="Выберите категорию" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    {categoryOptions.map((option) => (
                      <SelectItem
                        key={option.id}
                        value={option.id}
                        disabled={!option.isLeaf}
                        className={!option.isLeaf ? "text-muted-foreground" : undefined}
                      >
                        <span style={{ paddingLeft: `${option.depth * 12}px` }}>
                          {option.title}
                          {!option.isLeaf ? " (есть подкатегории)" : ""}
                        </span>
                      </SelectItem>
                    ))}
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>

            <div className="grid gap-2">
              <Label htmlFor="bulk-amount">Цена, ₽</Label>
              <Input
                id="bulk-amount"
                type="number"
                min={1}
                step="1"
                value={amount}
                onChange={(event) => setAmount(event.target.value)}
                disabled={uploading}
                required
              />
            </div>
          </div>

          <div
            role="button"
            tabIndex={0}
            onKeyDown={(event) => {
              if (event.key === "Enter" || event.key === " ") {
                inputRef.current?.click();
              }
            }}
            onDragEnter={(event) => {
              event.preventDefault();
              setIsDragging(true);
            }}
            onDragOver={(event) => {
              event.preventDefault();
              setIsDragging(true);
            }}
            onDragLeave={(event) => {
              event.preventDefault();
              setIsDragging(false);
            }}
            onDrop={(event) => {
              event.preventDefault();
              setIsDragging(false);
              if (event.dataTransfer.files.length > 0) {
                addFiles(event.dataTransfer.files);
              }
            }}
            onClick={() => inputRef.current?.click()}
            className={[
              "flex cursor-pointer flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed p-8 text-center transition-colors",
              isDragging
                ? "border-primary bg-primary/5"
                : "border-muted-foreground/30 hover:bg-muted/40",
              uploading ? "pointer-events-none opacity-60" : "",
            ].join(" ")}
          >
            <FileUp className="h-10 w-10 text-muted-foreground" />
            <div>
              <p className="font-medium">Перетащите файлы сюда или нажмите для выбора</p>
              <p className="mt-1 text-sm text-muted-foreground">
                Поддерживаются .doc и .docx, до 15 МБ каждый
              </p>
            </div>
            <input
              ref={inputRef}
              type="file"
              multiple
              accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
              className="hidden"
              disabled={uploading}
              onChange={(event) => {
                if (event.target.files) {
                  addFiles(event.target.files);
                  event.target.value = "";
                }
              }}
            />
          </div>

          {files.length > 0 && (
            <div className="rounded-md border">
              <div className="border-b px-4 py-2 text-sm font-medium">
                Выбрано файлов: {files.length}
              </div>
              <ul className="divide-y">
                {files.map((file) => (
                  <li
                    key={file.name}
                    className="flex items-center justify-between gap-3 px-4 py-2 text-sm"
                  >
                    <div className="min-w-0">
                      <p className="truncate font-medium">{file.name}</p>
                      <p className="text-muted-foreground">{formatFileSize(file.size)}</p>
                    </div>
                    <Button
                      type="button"
                      variant="ghost"
                      size="icon"
                      disabled={uploading}
                      onClick={() => removeFile(file.name)}
                      aria-label={`Удалить ${file.name}`}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {uploading && (
            <div className="space-y-2">
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">Загрузка на сервер…</span>
                <span className="font-medium">{progress}%</span>
              </div>
              <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                <div
                  className="h-full rounded-full bg-primary transition-all duration-200"
                  style={{ width: `${progress}%` }}
                />
              </div>
            </div>
          )}

          <div className="flex justify-end">
            <Button type="submit" disabled={uploading || files.length === 0 || !selectedCategoryId}>
              {uploading ? "Загрузка…" : `Загрузить (${files.length})`}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}
