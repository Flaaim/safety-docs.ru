import AdminBreadcrumbs from "@/components/Admin/Dashboard/AdminBreadcrumbs";
import { cookies } from "next/headers";
import React from "react";
import { getAllChildrenCategories } from "@api/category";
import LaunchParse from "@/components/Admin/Dashboard/Parser/launch-parse";

export default async function ParserPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const categories = await getAllChildrenCategories(token);

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Парсер" }]} />

      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Парсер</h1>
          <p className="text-sm text-muted-foreground mt-1">
            Введите данные в форму для парсинга документов
          </p>
        </div>
      </div>

      <LaunchParse categories={categories} token={token} />
    </div>
  );
}
