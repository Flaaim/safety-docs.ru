import {DirectionCollection, DirectionDTO} from "@/interfaces/direction.interface";
import {API} from "@/app/api";


export async function getDirectionBySlug(slug: string, token?: string): Promise<DirectionDTO> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.direction.getBySlug(slug), {
    method: 'GET',
    headers: headers,
    cache: token ? 'no-store' : 'force-cache'
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
  }
  return response.json();
}

export async function getAllDirections(token?: string): Promise<DirectionCollection> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.direction.getAll(), {
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

export async function addDirection(token: string | undefined, direction: object): Promise<void> {

  const response = await fetch(API.direction.add(), {
    method: "POST",
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
      slug: direction.slug
    })
  })
  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка отправки данных`);
  }
}

export async function updateDirection(token: string | undefined, id: string, direction: object): Promise<void> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }
  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }
  const response = await fetch(API.direction.update(id), {
    method: 'PUT',
    headers: headers,
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
      slug: direction.slug
    })
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка отправки данных`);
  }
}
