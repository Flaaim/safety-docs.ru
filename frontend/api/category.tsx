import {AssignCategory, CategoryCollection, CategoryDTO} from "@/interfaces/category.interface";
import {API} from "@/app/api";


export async function getAllCategories(token?: string): Promise<CategoryCollection> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.category.getAll(), {
    method: "GET",
    headers: headers,
    cache: token ? 'no-store' : 'force-cache'
  })

  if (!response.ok) {
    let errorMessage = 'Ошибка получения данных';

    try{
      const errorData = await response.json();
      if (errorData && errorData.message) {
        errorMessage = errorData.message;
      }

    }catch (e) {
      console.error("Не удалось распарсить JSON ошибки:", e);
    }
    throw new Error(errorMessage);
  }

  return response.json();

}

export async function addCategory(token: string | undefined, category:Partial<CategoryDTO>): Promise<void>{
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }
  const directionId = (category.directionId ? category.directionId : '')
  const response = await fetch(API.category.add(directionId), {
    method: "POST",
    headers: headers,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      slug: category.slug,
    })
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка отправки данных`);
  }

}

export async function getCategoryBySlug(slug: string, directionId:string, token: string | undefined): Promise<CategoryDTO>{
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.category.getBySlug(slug, directionId), {
    method: "GET",
    headers: headers
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
  }
  return response.json();
}

export async function updateCategory(token: string | undefined, category:Partial<CategoryDTO>): Promise<void>{
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }
  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const id = (category.id) ? category.id : '';
  const directionId = (category.directionId) ? category.directionId : '';

  const response = await fetch(API.category.update(id, directionId), {
    method: 'PUT',
    headers: headers,
    body: JSON.stringify({
      title: category.title,
      description: category.description,
      text: category.text,
      slug: category.slug
    })
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка отправки данных`);
  }
}

export async function assignProduct(token: string | undefined, data: AssignCategory): Promise<void> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }
  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.category.assignProduct(data.categoryId), {
    method: "PUT",
    headers: headers,
    body: JSON.stringify({productId: data.productId})
  })

  if (!response.ok) {
    let errorMessage = "Ошибка отправки данных";

    try {
      const errorData = await response.json();

      if (errorData && errorData.message) {
        errorMessage = errorData.message;
      }

    }catch (e) {
      console.error("Не удалось распарсить JSON ошибки:", e);
    }
    throw new Error(errorMessage);
  }
}
