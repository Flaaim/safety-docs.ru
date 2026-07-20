import { Category, CategoryChildren } from "@/types/category";
import { API } from "@/app/api";
import { apiFetch } from "@api/apiClient";

const publicRevalidate = 3600;

/** Preferred public API — mirrors /directions/[directionSlug] */
export async function getCategoriesByDirectionSlug(
  directionSlug: string,
  token?: string
): Promise<Category[]> {
  return await apiFetch<Category[]>(API.category.getAllByDirectionSlug(directionSlug), {
    method: "GET",
    token,
    ...(token
      ? { cache: "no-store" }
      : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
  });
}

/** Preferred public API — mirrors /directions/[directionSlug]/[categorySlug] */
export async function getCategoryBySlugs(
  directionSlug: string,
  categorySlug: string,
  token?: string
): Promise<Category> {
  return await apiFetch<Category>(API.category.getBySlugs(directionSlug, categorySlug), {
    method: "GET",
    token,
    ...(token
      ? { cache: "no-store" }
      : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
  });
}

/** Admin: list by direction UUID (add/edit category dialogs) */
export async function getCategoriesByDirection(
  directionId: string,
  token?: string
): Promise<Category[]> {
  return await apiFetch<Category[]>(API.category.getAllByDirection(directionId), {
    method: "GET",
    token,
    ...(token
      ? { cache: "no-store" }
      : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
  });
}

export async function addCategory(
  token: string | undefined,
  category: Partial<Category>
): Promise<void> {
  const directionId = category.directionId || "";

  return await apiFetch<void>(API.category.add(directionId), {
    method: "POST",
    token,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      parentId: category.parentId,
    }),
  });
}

/** Admin: get by slug under direction UUID (edit-category dialog) */
export async function getCategoryBySlug(
  slug: string,
  directionId: string,
  token?: string
): Promise<Category> {
  return await apiFetch<Category>(API.category.getBySlug(slug, directionId), {
    method: "GET",
    token,
    ...(token
      ? { cache: "no-store" }
      : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
  });
}

export async function updateCategory(
  token: string | undefined,
  category: Partial<Category>
): Promise<void> {
  const id = category.id || "";
  const directionId = category.directionId || "";

  return await apiFetch<void>(API.category.update(id, directionId), {
    method: "PUT",
    token,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      parentId: category.parentId !== undefined ? category.parentId : null,
      directionId: category.directionId,
    }),
  });
}

export async function deleteCategory(
  token: string | undefined,
  categoryId: string,
  directionId: string
): Promise<void> {
  return await apiFetch<void>(API.category.delete(categoryId, directionId), {
    method: "DELETE",
    token,
  });
}

export async function getAllChildrenCategories(
  token: string | undefined
): Promise<CategoryChildren[]> {
  return await apiFetch<CategoryChildren[]>(API.category.getAllChildrenCategories(), {
    method: "GET",
    token,
  });
}
