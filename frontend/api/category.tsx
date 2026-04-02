import {AssignCategory, CategoryCollection, CategoryDTO} from "@/interfaces/category.interface";
import {API} from "@/app/api";
import {apiFetch} from "@api/apiClient";

export async function getAllCategories(token?: string): Promise<CategoryCollection> {
 return await apiFetch<CategoryCollection>(API.category.getAll(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  })
}

export async function addCategory(token: string | undefined, category:Partial<CategoryDTO>): Promise<void>{

  const directionId = category.directionId || '';

  return await apiFetch<void>(API.category.add(directionId), {
    method: "POST",
    token,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      slug: category.slug,
    })
  })
}

export async function getCategoryBySlug(slug: string, directionId:string, token: string | undefined): Promise<CategoryDTO>{

  return await apiFetch<CategoryDTO>(API.category.getBySlug(slug, directionId), {
    method: "GET",
    token
  })
}

export async function updateCategory(token: string | undefined, category:Partial<CategoryDTO>): Promise<void>{
  const id = category.id || '';
  const directionId = category.directionId || '';

  return await apiFetch<void>(API.category.update(id, directionId), {
    method: 'PUT',
    token,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      slug: category.slug
    })
  })
}

export async function assignProduct(token: string | undefined, data: AssignCategory): Promise<void> {

  return await apiFetch<void>(API.category.assignProduct(data.categoryId), {
    method: "PUT",
    token,
    body: JSON.stringify({productId: data.productId})
  })
}

export async function refuseProduct(token: string | undefined, categoryId: string): Promise<void> {

  return await apiFetch<void>(API.category.refuseProduct(categoryId), {
    method: "DELETE",
    token
  })
}
