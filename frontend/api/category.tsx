import { AssignCategory, Category, CategoryCollection } from "@/types/category";
import { API } from "@/app/api";
import { apiFetch } from "@api/apiClient";

const publicCache: RequestCache = "no-store";

export async function getAllCategories(token?: string): Promise<CategoryCollection> {
  return await apiFetch<CategoryCollection>(API.category.getAll(), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
  });
}

/** Preferred public API — mirrors /directions/[directionSlug] */
export async function getCategoriesByDirectionSlug(
  directionSlug: string,
  token?: string
): Promise<Category[]> {
  return await apiFetch<Category[]>(API.category.getAllByDirectionSlug(directionSlug), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
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
    cache: token ? "no-store" : publicCache,
  });
}

/** @deprecated Prefer getCategoriesByDirectionSlug for public pages */
export async function getCategoriesByDirection(
  directionId: string,
  token?: string
): Promise<Category[]> {
  return await apiFetch<Category[]>(API.category.getAllByDirection(directionId), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
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

/** @deprecated Prefer getCategoryBySlugs for public pages */
export async function getCategoryBySlug(
  slug: string,
  directionId: string,
  token?: string
): Promise<Category> {
  return await apiFetch<Category>(API.category.getBySlug(slug, directionId), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
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

export async function assignProduct(
  token: string | undefined,
  data: AssignCategory
): Promise<void> {
  return await apiFetch<void>(API.category.assignProduct(data.categoryId), {
    method: "PUT",
    token,
    body: JSON.stringify({ productId: data.productId }),
  });
}

export async function refuseProduct(token: string | undefined, categoryId: string): Promise<void> {
  return await apiFetch<void>(API.category.refuseProduct(categoryId), {
    method: "DELETE",
    token,
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
