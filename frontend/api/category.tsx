import {CategoryCollection, CategoryDTO} from "@/interfaces/category.interface";
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
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
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
