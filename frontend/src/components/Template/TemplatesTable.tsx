import Link from "next/link";
import { FileText, Calendar, ChevronRight } from "lucide-react";
import { TemplateItem } from "@/interfaces/template.interface";

interface TemplatesTableProps {
  templates: TemplateItem[];
  directionSlug: string;
  categorySlug: string;
}

export default function TemplatesTable({
  templates,
  directionSlug,
  categorySlug,
}: TemplatesTableProps) {
  const formatDate = (dateInput?: string | Date) => {
    if (!dateInput) return "—";
    const date = typeof dateInput === "string" ? new Date(dateInput) : dateInput;
    return new Intl.DateTimeFormat("ru-RU", {
      day: "numeric",
      month: "short",
      year: "numeric",
    }).format(date);
  };

  return (
    <div className="border rounded-lg overflow-hidden bg-card shadow-sm">
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b bg-muted/40 text-xs font-medium text-muted-foreground uppercase tracking-wider">
              <th className="py-3 px-4">Название документа</th>
              <th className="py-3 px-4 whitespace-nowrap">Дата добавления</th>
              <th className="py-3 px-4 w-10"></th>
            </tr>
          </thead>
          <tbody className="divide-y text-sm">
            {templates.map((template) => (
              <tr key={template.id} className="group hover:bg-accent/50 transition-colors">
                <td className="py-3 px-4 max-w-md">
                  <Link
                    href={`/directions/${directionSlug}/${categorySlug}/${template.slug}`}
                    className="flex items-start gap-3 focus:outline-none"
                  >
                    <FileText className="h-5 w-5 text-primary/70 shrink-0 mt-0.5" />
                    <div className="min-w-0">
                      <div className="font-medium text-foreground group-hover:text-primary transition-colors whitespace-normal break-words">
                        {template.name}
                      </div>
                      <div className="text-xs text-muted-foreground truncate mt-0.5 font-mono">
                        {template.filename}
                      </div>
                    </div>
                  </Link>
                </td>
                <td className="py-3 px-4 whitespace-nowrap text-muted-foreground text-xs">
                  <div className="flex items-center gap-1.5">
                    <Calendar className="h-3.5 w-3.5 opacity-70" />
                    {formatDate(template.createdAt)}
                  </div>
                </td>
                <td className="py-3 px-4 text-right">
                  <Link
                    href={`/directions/${directionSlug}/${categorySlug}/${template.slug}`}
                    className="inline-flex p-1 rounded-md text-muted-foreground group-hover:text-foreground group-hover:translate-x-0.5 transition-all"
                  >
                    <ChevronRight className="h-4 w-4" />
                  </Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
