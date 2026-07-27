"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { ChevronRight, FileText } from "lucide-react";
import { getRelatedDocuments } from "@api/document";
import { TemplateItem } from "@/interfaces/template.interface";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";

interface RelatedTemplatesProps {
  documentId: string;
  directionSlug: string;
  categorySlug: string;
  className?: string;
}

const formatPrice = (amount: number) =>
  new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    maximumFractionDigits: 0,
  }).format(amount);

export default function RelatedTemplates({
  documentId,
  directionSlug,
  categorySlug,
  className,
}: RelatedTemplatesProps) {
  const [items, setItems] = useState<TemplateItem[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!documentId) {
      return () => {};
    }

    const abortController = new AbortController();

    const fetchRelated = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const data = await getRelatedDocuments(documentId);
        if (!abortController.signal.aborted) {
          setItems(data.items ?? []);
        }
      } catch (err: unknown) {
        if (abortController.signal.aborted) {
          return;
        }
        // Backend throws DomainException when nothing found — treat as empty list
        const message = err instanceof Error ? err.message : "";
        if (message.toLowerCase().includes("no related documents")) {
          setItems([]);
          setError(null);
        } else {
          setError(message || "Не удалось загрузить похожие документы");
          setItems([]);
        }
      } finally {
        if (!abortController.signal.aborted) {
          setIsLoading(false);
        }
      }
    };

    void fetchRelated();

    return () => abortController.abort();
  }, [documentId]);

  if (!isLoading && !error && items.length === 0) {
    return null;
  }

  return (
    <Card className={cn("overflow-hidden", className)}>
      <CardHeader className="border-b py-4">
        <CardTitle className="text-base">Похожие документы</CardTitle>
        <CardDescription>
          Другие шаблоны из этой категории, которые могут вам пригодиться
        </CardDescription>
      </CardHeader>

      <CardContent className="p-0">
        {isLoading && (
          <ul className="divide-y">
            {Array.from({ length: 3 }).map((_, index) => (
              <li key={index} className="flex items-start gap-3 px-4 py-3 sm:px-6">
                <Skeleton className="mt-0.5 h-5 w-5 shrink-0 rounded" />
                <div className="min-w-0 flex-1 space-y-2">
                  <Skeleton className="h-4 w-4/5" />
                  <Skeleton className="h-3 w-1/3" />
                </div>
              </li>
            ))}
          </ul>
        )}

        {!isLoading && error && (
          <p className="px-4 py-6 text-sm text-muted-foreground sm:px-6">{error}</p>
        )}

        {!isLoading && !error && items.length > 0 && (
          <ul className="divide-y">
            {items.map((template) => (
              <li key={template.id}>
                <Link
                  href={`/directions/${directionSlug}/${categorySlug}/${template.slug}`}
                  className="group flex items-start gap-3 px-4 py-3 transition-colors hover:bg-accent/50 sm:px-6"
                >
                  <FileText className="mt-0.5 h-5 w-5 shrink-0 text-primary/70" />
                  <div className="min-w-0 flex-1">
                    <div className="font-medium text-foreground transition-colors group-hover:text-primary">
                      {template.name}
                    </div>
                    <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                      <span>{formatPrice(template.amount)}</span>
                      <span className="truncate font-mono">{template.filename}</span>
                    </div>
                  </div>
                  <ChevronRight className="mt-1 h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-foreground" />
                </Link>
              </li>
            ))}
          </ul>
        )}
      </CardContent>
    </Card>
  );
}
