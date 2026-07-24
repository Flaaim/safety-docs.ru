"use client";

import React, { useEffect, useState } from "react";
import { Lock } from "lucide-react";
import { preview } from "@api/document";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";

interface TemplatePreviewProps {
  documentId: string;
  className?: string;
  /** Max visible height of the document teaser (px). */
  maxHeight?: number;
}

export default function TemplatePreview({
  documentId,
  className,
  maxHeight = 420,
}: TemplatePreviewProps) {
  const [htmlContent, setHtmlContent] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!documentId) {
      return () => {};
    }

    const abortController = new AbortController();

    const fetchPreview = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const htmlPreview = await preview(documentId);
        if (!abortController.signal.aborted) {
          setHtmlContent(htmlPreview.html);
        }
      } catch (err: unknown) {
        if (abortController.signal.aborted) {
          return;
        }
        setError(err instanceof Error ? err.message : "Не удалось загрузить превью");
      } finally {
        if (!abortController.signal.aborted) {
          setIsLoading(false);
        }
      }
    };

    void fetchPreview();

    return () => abortController.abort();
  }, [documentId]);

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.ctrlKey || e.metaKey) {
      const forbiddenKeys = ["c", "p", "s", "u", "a"];
      if (forbiddenKeys.includes(e.key.toLowerCase())) {
        e.preventDefault();
      }
    }
  };

  return (
    <Card className={cn("overflow-hidden gap-0 py-0", className)}>
      <CardHeader className="border-b py-4">
        <CardTitle className="text-base">Предварительный просмотр</CardTitle>
        <CardDescription>
          Показан фрагмент документа. Полный файл доступен после оплаты.
        </CardDescription>
      </CardHeader>

      <CardContent className="p-0">
        {isLoading && (
          <div className="space-y-3 p-6" style={{ minHeight: maxHeight }}>
            <Skeleton className="h-5 w-2/3" />
            <Skeleton className="h-4 w-full" />
            <Skeleton className="h-4 w-11/12" />
            <Skeleton className="h-4 w-4/5" />
            <Skeleton className="h-4 w-full" />
            <Skeleton className="h-4 w-3/4" />
          </div>
        )}

        {!isLoading && error && (
          <div
            className="flex items-center justify-center p-8 text-sm text-destructive"
            style={{ minHeight: maxHeight }}
          >
            Ошибка: {error}
          </div>
        )}

        {!isLoading && !error && (
          <div className="relative bg-muted/40" style={{ height: maxHeight }}>
            <div
              className="h-full overflow-hidden px-4 py-5 sm:px-6"
              style={{
                userSelect: "none",
                WebkitUserSelect: "none",
                MozUserSelect: "none",
                msUserSelect: "none",
              }}
              onContextMenu={(e) => e.preventDefault()}
              onCopy={(e) => e.preventDefault()}
              onCut={(e) => e.preventDefault()}
              onKeyDown={handleKeyDown}
              tabIndex={0}
              aria-label="Фрагмент документа для предварительного просмотра"
            >
              <div className="mx-auto w-full max-w-3xl rounded-md bg-white p-6 shadow-sm sm:p-8">
                <div
                  dangerouslySetInnerHTML={{ __html: htmlContent || "" }}
                  className="prose prose-sm max-w-none text-black focus:outline-none prose-headings:text-black prose-p:text-black"
                />
              </div>
            </div>

            <div
              className="pointer-events-none absolute inset-x-0 bottom-0 flex h-40 flex-col items-center justify-end bg-gradient-to-t from-background via-background/90 to-transparent pb-5"
              aria-hidden
            >
              <div className="pointer-events-auto inline-flex items-center gap-2 rounded-full border bg-background/95 px-3 py-1.5 text-xs text-muted-foreground shadow-sm backdrop-blur">
                <Lock className="h-3.5 w-3.5" />
                Полный документ — после оплаты
              </div>
            </div>
          </div>
        )}
      </CardContent>
    </Card>
  );
}
